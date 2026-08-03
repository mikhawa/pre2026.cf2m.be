# 124 — Mot de passe oublié pour utilisateur non connecté

**Date** : 2026-07-31
**Branche** : `feature/27-test-other-design`

## Besoin
Une revue UI/UX du front public a révélé que le lien "Mot de passe oublié ?" de la page
de connexion (`security/login.html.twig`) pointait vers `href="#"` : aucune route
publique n'existait pour qu'un visiteur déconnecté demande la réinitialisation de son
mot de passe. Le seul flux existant (`ProfileController::requestPasswordReset`, voir
[073](073-2026-03-18_16-00_Reinitialisation-mot-de-passe-depuis-profil.md)) exige d'être
déjà connecté.

## Flux implémenté

```
/connexion → lien "Mot de passe oublié ?"
    → GET /mot-de-passe-oublie (SecurityController, public)
        → affiche formulaire (email + Turnstile)

POST /mot-de-passe-oublie
    → valide CSRF ('forgot_password')
    → vérifie le champ email non vide
    → vérifie Turnstile (TurnstileVerifier, même service que Contact/Inscription)
    → recherche l'utilisateur par email (UserRepository::findOneBy(['email' => ...]))
        → si trouvé : génère token 64 chars, stocke en BDD avec timestamp,
          envoie templates/emails/reset_password.html.twig (réutilisé tel quel)
        → dans tous les cas (trouvé ou non) : flash générique + redirect /connexion

Suite du flux : identique à l'existant
    → Email → lien GET /reinitialisation-mot-de-passe/{token} (déjà en place, voir 073)
```

Le message flash est volontairement identique que l'email corresponde ou non à un
compte existant, pour ne pas permettre l'énumération des comptes via ce formulaire.

## Fichiers modifiés/créés

| Fichier | Action |
|---|---|
| `src/Controller/SecurityController.php` | Constructeur `MailerInterface` + `MAIL_FORM` + `TurnstileVerifier` ; nouvelle route `GET\|POST /mot-de-passe-oublie` (`app_forgot_password`) |
| `templates/security/forgot_password.html.twig` | Nouveau — formulaire email + Turnstile, sur le modèle visuel de `login.html.twig`/`reset_password.html.twig` |
| `templates/security/login.html.twig` | Lien "Mot de passe oublié ?" : `href="#"` → `{{ path('app_forgot_password') }}` |
| `templates/profil/index.html.twig` | Correction indépendante (voir plus bas) |

## Sécurité
- Même génération de token que le flux existant : `bin2hex(random_bytes(32))` = 64 chars hex
- Anti-énumération : message flash générique, qu'un compte existe ou non pour l'email saisi
- Anti-robot : Cloudflare Turnstile (`data-theme="auto"`, s'adapte au thème clair/sombre du site)
- Pas de nouvelle règle `access_control` nécessaire : la route est hors de `^/profil`, `^/admin`, `^/double-authentification`, donc publique par défaut

## Correction indépendante — attribut `class` dupliqué
Repérée dans la même revue UI/UX : `templates/profil/index.html.twig` déclarait trois fois
`<h3 class="h6 text-uppercase mb-2" class="cf2m-muted-label">` (sections "Présentation",
"Formations", "Liens"). Un navigateur ignore le second attribut `class` sur un même tag,
donc le style `cf2m-muted-label` ne s'appliquait jamais. Comparer avec
`templates/profil/public.html.twig` qui fusionne correctement les classes en une seule
liste (`class="h6 text-uppercase cf2m-muted-label mb-2"`). Corrigé en fusionnant les
listes de classes sur les trois occurrences.

## Test manuel effectué (Mailpit)
Flux vérifié de bout en bout via `curl` + l'API Mailpit, sans modifier de mot de passe réel :
1. `GET /mot-de-passe-oublie` → formulaire rendu, CSRF récupéré.
2. `POST` avec un email existant + clé de test Turnstile "always passes" → 302 vers `/connexion`.
3. Flash générique affiché sur `/connexion`.
4. Email reçu dans Mailpit (`localhost:8025`), lien correct, token de 64 caractères hex confirmé en BDD.
5. `GET` sur le lien de réinitialisation → 200, formulaire "Nouveau mot de passe" rendu sans erreur (token valide, non expiré).
6. Étape finale (soumission du nouveau mot de passe) volontairement non testée pour ne pas modifier un compte réel — à vérifier manuellement.
