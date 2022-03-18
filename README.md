# Lebackoffice by ecowan
![Logo ecowan](https://github.com/ecowan-fr/lebackoffice/blob/main/public/images/logo/vert-blanc/logo-vert-clair.svg?raw=true)

**Le backoffice est une application web qui tourne en HTML / PHP 8 / Javascript.**

L'application utilise le Framework Symfony.

## Licences de l'application
|||Community|Premium|
|-|-|:-------------:|:-------------:
|**Organismes**|Particulier|✅|✅|
||Association loi 1901|✅|✅|
||Société|❌|✅|
|**Licence**|obligatoire|✅|✅|
|**Fonctionnalités**|a définir|||
|**Prix**|par mois|Gratuit|**4.99 €**|
||par an|Gratuit|**49.99 €**|

## Installation d'un environnement de développement
### Prérequis
 1. [Symfony CLI](https://symfony.com/download)
 2. PHP 8.1 ou supérieur et ces extensions PHP (qui sont installées et activées par défaut dans la plupart des installations PHP 8)
	 1. Rewrite
	 2. INTL
	 3. PDO Mysql
	 4. CURL
	 5. Ctype
	 6. iconv
	 7. PCRE
	 8. Session
	 9. SimpleXML
	 10. Tokenizer
	 11. OPcache
	 12. Fileinfo
 3. Composer
 4. Git
 5. Node JS
 6. Docker
 7. Docker Compose

### Démarrage de l'environnement de développement
```bash
composer install --optimize-autoloader
npm install
npm run build
docker-compose up -d
symfony console app:setup
symfony serve -d
```

## Suivis du projet
Vous pouvez suivre le projet via les [issues](https://github.com/ecowan-fr/lebackoffice/issues). Toutes les taches en cours et a suivre sont présentent exclusivement sur Github.
Nous avons également un Trello où sont postées nos idées. Vous pouvez y participer en postant des commentaires : https://trello.com/b/wCttkBDI/lebackoffice-by-ecowan

## Support
Pour toutes questions, vous pouvez envoyer un email à hello@ecowan.fr ou [ouvrir une issue](https://github.com/ecowan-fr/lebackoffice/issues/new).

## Licence
En installant l'application, vous acceptez les [conditions générales d'utilisation](https://github.com/ecowan-fr/lebackoffice/blob/main/terms.md).