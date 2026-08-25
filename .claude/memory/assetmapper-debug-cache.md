---
name: assetmapper-debug-cache
description: En dev, Symfony AssetMapper (debug=true) ne resert pas un asset modifié tant que son fichier compilé existe encore dans public/assets — un simple rm ne suffit pas, il faut recompiler
metadata:
  type: project
---

## Piège : CSS/JS modifiés mais pas pris en compte en dev
Après avoir édité `assets/styles/app.css` (ou tout autre asset), le navigateur peut continuer à recevoir l'ancienne version compilée. Constaté le 2026-08-25 pendant [[navbar-convention]] (revirement navbar/footer en light mode) : plusieurs correctifs CSS successifs n'étaient pas servis malgré des `curl` répétés, un `rm -rf public/assets/*` et un `bin/console cache:clear`.

**Cause** : en mode debug, `bin/console asset-map:compile` (ou la première résolution runtime) écrit un fichier hashé dans `public/assets/` ; tant que ce fichier existe, Symfony le sert tel quel sans revérifier le contenu source, même après un simple `rm` du dossier — le warning affiché après compile le dit explicitement : *"Symfony will not serve any changed assets until you delete the files in the public/assets directory again."*

**Solution fiable** : après une modification d'asset en dev, exécuter :
```bash
docker compose exec php php bin/console asset-map:compile --env=dev
```
Vérifier que le hash de fichier référencé dans le `<head>` (`curl -s http://localhost:8085/ | grep -oP 'href="[^"]*app[^"]*\.css[^"]*"'`) a bien changé avant de conclure qu'un correctif CSS ne fonctionne pas — sinon on risque de diagnostiquer à tort un bug CSS inexistant.
