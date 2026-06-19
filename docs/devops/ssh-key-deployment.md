# Clé SSH de déploiement — Création et renouvellement

**Contexte** : Le déploiement automatisé (GitHub Actions via `appleboy/ssh-action`) s'authentifie sur le VPS avec une paire de clés SSH. La clé privée est stockée dans le secret GitHub `PROD_SSH_PRIVATE_KEY`, la clé publique correspondante doit être dans le fichier `~/.ssh/authorized_keys` de l'utilisateur de déploiement sur le VPS.

---

## 1. Générer une nouvelle paire de clés

Exécuter **en local** (pas sur le VPS) :

```bash
ssh-keygen -t ed25519 -C "deploy-cf2m-prod" -f ~/.ssh/cf2m_prod_deploy
```

- `-t ed25519` : algorithme moderne, recommandé
- `-C "deploy-cf2m-prod"` : commentaire pour identifier la clé
- `-f ~/.ssh/cf2m_prod_deploy` : nom du fichier (ne pas écraser une clé existante)
- **Ne pas mettre de passphrase** (GitHub Actions ne peut pas interagir pour la saisir)

Cela crée deux fichiers :
- `~/.ssh/cf2m_prod_deploy` → **clé privée** (à mettre dans GitHub Secrets)
- `~/.ssh/cf2m_prod_deploy.pub` → **clé publique** (à déposer sur le VPS)

---

## 2. Déposer la clé publique sur le VPS

### Option A — via ssh-copy-id (recommandé si accès SSH actuel)

```bash
ssh-copy-id -i ~/.ssh/cf2m_prod_deploy.pub PROD_VPS_USER@PROD_VPS_HOST
```

Remplacer `PROD_VPS_USER` et `PROD_VPS_HOST` par les valeurs réelles.

### Option B — manuellement via Plesk ou SSH existant

Se connecter au VPS et ajouter la clé publique :

```bash
# Se connecter au VPS (avec un accès existant)
ssh PROD_VPS_USER@PROD_VPS_HOST

# Sur le VPS
mkdir -p ~/.ssh
chmod 700 ~/.ssh
cat >> ~/.ssh/authorized_keys << 'EOF'
<contenu de cf2m_prod_deploy.pub>
EOF
chmod 600 ~/.ssh/authorized_keys
```

### Option C — via l'interface Plesk

Dans Plesk → **Websites & Domains** → domaine concerné → **SSH Keys** → ajouter la clé publique.

---

## 3. Mettre à jour le secret GitHub

1. Copier le contenu de la clé **privée** :
   ```bash
   cat ~/.ssh/cf2m_prod_deploy
   ```

2. Sur GitHub : **Settings → Secrets and variables → Actions**

3. Mettre à jour (ou créer) le secret `PROD_SSH_PRIVATE_KEY` avec le contenu copié.
   - Inclure les lignes `-----BEGIN OPENSSH PRIVATE KEY-----` et `-----END OPENSSH PRIVATE KEY-----`

---

## 4. Tester la connexion

Avant de déclencher un déploiement, vérifier que la clé fonctionne :

```bash
ssh -i ~/.ssh/cf2m_prod_deploy -o StrictHostKeyChecking=no PROD_VPS_USER@PROD_VPS_HOST "echo OK"
```

Résultat attendu : `OK`

---

## 5. Vérifier le déploiement GitHub Actions

Pousser un commit sur la branche `production` et contrôler les logs GitHub Actions dans l'onglet **Actions** du dépôt. L'étape **Déploiement SSH** doit se terminer en succès.

---

## Secrets GitHub requis (rappel)

| Secret | Valeur |
|--------|--------|
| `PROD_VPS_HOST` | IP ou hostname du VPS prod |
| `PROD_VPS_USER` | Utilisateur SSH du domaine Plesk (ex : `cf2mbe`) |
| `PROD_SSH_PRIVATE_KEY` | Contenu complet de la clé privée ED25519 |
| `PROD_VPS_PATH` | Chemin absolu du répertoire sur le VPS (ex : `/var/www/vhosts/cf2m.be/production.cf2m.be`) |

---

## Diagnostic en cas d'échec

| Symptôme | Cause probable | Action |
|----------|---------------|--------|
| `Permission denied (publickey)` | Clé publique absente ou mal formatée dans `authorized_keys` | Vérifier `~/.ssh/authorized_keys` sur le VPS |
| `Host key verification failed` | Clé hôte du VPS inconnue de GitHub | Normal la 1ère fois ; `ssh-action` utilise `StrictHostKeyChecking=no` par défaut |
| `bad permissions` sur `authorized_keys` | Droits trop ouverts | `chmod 600 ~/.ssh/authorized_keys && chmod 700 ~/.ssh` |
| Secret mal copié (retour à la ligne manquant) | GitHub supprime parfois le `\n` final | Coller la clé en une seule opération copier-coller |

---

## Suppression d'une ancienne clé

Sur le VPS, éditer `~/.ssh/authorized_keys` et supprimer la ligne contenant `deploy-cf2m-prod` (ou l'ancien commentaire) avant d'ajouter la nouvelle.

```bash
# Afficher les clés autorisées
cat ~/.ssh/authorized_keys

# Supprimer la ligne contenant l'ancien commentaire
sed -i '/deploy-cf2m-prod/d' ~/.ssh/authorized_keys
```
