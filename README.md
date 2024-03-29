# Lebackoffice by ecowan [![security-checker](https://github.com/ecowan-fr/lebackoffice/actions/workflows/main.yml/badge.svg?branch=reset-password)](https://github.com/ecowan-fr/lebackoffice/actions/workflows/main.yml)
![Logo ecowan](https://github.com/ecowan-fr/lebackoffice/blob/main/public/images/logo/logo-lebackoffice-blanc.svg?raw=true)

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
 2. [PHP 8.1](https://www.php.net/downloads) ou supérieur et ses extensions :
	 1. [Intl](https://www.php.net/book.intl)
	 2. [PDO + PDO Mysql](https://www.php.net/book.pdo)
	 3. [cURL](https://www.php.net/book.curl)
	 4. [Ctype](https://www.php.net/book.ctype)
	 5. [iconv](https://www.php.net/book.iconv)
	 6. [PCRE](https://www.php.net/book.pcre)
	 7. [Session](https://www.php.net/book.session)
	 8. [SimpleXML](https://www.php.net/book.simplexml)
	 9. [Tokenizer](https://www.php.net/book.tokenizer)
	 10. [OPcache](https://www.php.net/book.opcache)
	 11. [Fileinfo](https://www.php.net/book.fileinfo)
	 12. [OpenSSL](https://www.php.net/book.openssl)
	 13. [Bcmath](https://www.php.net/book.bc)
	 14. [GMP](https://www.php.net/book.gmp)
 3. [Composer](https://getcomposer.org/doc/00-intro.md)
 4. Git
 5. [Node JS - Version >=11](https://nodejs.org)
 6. [Docker + Docker Compose](https://www.docker.com/)
 7. [OpenSSL](https://www.openssl.org/)

### Démarrage de l'environnement de développement
```bash
composer install --optimize-autoloader
npm install --force
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