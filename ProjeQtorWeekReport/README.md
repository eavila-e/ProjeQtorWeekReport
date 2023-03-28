# ProjeQtOr_Deploy

Outils de déploiement du nouveau plan de charges sur ProjeQtOr

# Instructions de déploiement:

A) Posser tous les fichiers dans le dossier projeqtor du repertoir WEB du serveur (htdocs).
Fichiers à déposer : 
    1) deploy.sh
    2) langscript.py
    3) resourcePlanWeekly.php
    4) HeaderCalc.php
    5) parametersDeploiement.sql

B) Lancer un terminal et déclencher deploy.sh, l'utilisation du script est décrite ci-dessous

AUTHOR : Esteban AVILA

Entreprise : COEXYA
   
   Script de mise à jour et deploiement du plan hebdomadaire custom
   S'assurer d'avoir déjà téléchargé le dossier projeqtor de version
   superieur et de la mettre au même endroit que ce script

   SUITE DE PAS SUIVIS PAR LE SCRIPT
   -----!! EXECUTE AS ADMINISTRATOR !!------

# DEPLOYMENT 
   ./deploy.sh 1 0

   1) Copier le fichier 'HeaderCalc.php' dans le dossier 'projeqtor/model/custom/' 
   2) Copier le fichier 'resourcePlanWeekly.php' dans le dossier 'projeqtor/report/' 
   3) Lance le scripr langscript.py

# MAJ 

  ./deploy.sh 0 #dossier projeqtorV920#


   1) Eteindre le service Apache2.4 
   2) Copier le fichier 'projeqtor/files/config/parameters.php' dans le dossier courrante 
   3) Effacer toute l'arborescence projeqtor 
   4) Copier la nouvelle arborescence 
   5) Copier le fichier 'parameters.php' dans 'projeqtor/files/config/' 
   6) Copier le fichier 'HeaderCalc.php' dans le dossier 'projeqtor/model/custom/' 
   7) Copier le fichier 'resourcePlanWeekly.php' dans le dossier 'projeqtor/report/' 
   8) Lancement script langscript.py   
   9) Start les services Apache2.4 


