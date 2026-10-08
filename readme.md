![wordpress](https://img.shields.io/badge/wordpress-v6.7.1-0678BE.svg?style=flat-square)
![php](https://img.shields.io/badge/PHP-v8.3-828cb7.svg?style=flat-square)
![composer](https://img.shields.io/badge/composer-v2-126E75.svg?style=flat-square)
![docker](https://img.shields.io/badge/docker-compose-2496ED.svg?style=flat-square)

## ABOUT
An advanced blog Wordpres project about coding by [Noweh](https://github.com/noweh/) and [Rapkalin](https://github.com/Rapkalin/).

## INSTALLATION

Le projet tourne dans Docker. Aucun PHP, Composer, MySQL ni vHost Apache n'est
requis sur la machine.

```
git clone git@github.com:Rapkalin/explain-code.git .
cp .env.example .env
docker compose up -d
```

Front : http://localhost:8030 — admin, base, mails, import d'un dump et pièges
connus : voir **[readme/docker.md](readme/docker.md)**.

## FRONTEND
- For each update of the newsmatic theme, you have to change/refresh website/app/themes/newsmatic-child/assets/js/theme.js to reflect the change

## TRANSLATIONS
The explain-code.pot file is the website's base language 
- Download and open the free [poedit](https://poedit.net/) software
- Translation functions to use:
  - __: Translate
  - _e: Translate and displays
  - _n: Translate and displays the plural
  
### ADD A NEW LANGUAGE
If the translation file doesn't exist in the language you want:
- Open the Poedit software
- Create a new file from the _explain-code.pot_ file to retrieve all words to translate
- Name it with the code needed code langage. Example: en_US

### ADD A NEW WORD TO TRANSLATE
If you want to add a new word to translate:
- Open the _explain-code.pot_ file with [poedit](https://poedit.net/)
- Go to the translate (or catalog) menu
- Click on update source code to retrieve all new translations added in the code

## MEANING OF SOME DIRECTORIES AND FILES

### WEBSITE/APP
This directory replace the wordpress-core/wp-content native Wordpress directory. 
This is where you will find all the plugins, themes etc:
- W3 Super Cache: this plugin install a few files and directories:
  - cache
  - w3tc-config
  - advanced-cache.php
- Languages: directory that handle the translations of your website. It is created by Wordpress when you configure the default language of your Wordpress website.
- Uploads: contains all the website's media files
- Plugins and themes: where are all the plugins & themes and custom plugins & themes/child-themes

## HOW TO DEPLOY
To use the auto-deploy using Github Workflows please follow the below instructions:
- Commit and push your branch (feature/xxx) to main
- Wait for approval and merge
- Once the PR approved and merged, pull the changes from main
```
  git checkout main
  git pull
```
- Create the new tag after checking the last published here: [Github Actions](https://github.com/Rapkalin/explain-code/actions)
```
  git tag x.x.x
```
- Push the new tag, this will deploy the main branch automatically to prod
```
  git push --tags
```