#
#   AUTHOR: Esteban AVILA
#           COEXYA
#   
#   Script de mise à jour et deploiement du plan hebdomadaire custom
#   S'assurer d'avoir déjà téléchargé le dossier projeqtor de version
#   superieur et de la mettre au même endroit que ce script
#
#   SUITE DE PAS SUIVIS PAR LE SCRIPT
#   -----!! EXECUTE AS ADMINISTRATOR !!------
#
##################### DEPLOYMENT ###################################################################################
#
#   ./deploy.sh 1 0 v
#
#  #  #  #  #  #  #  #  #  #  #  #  #  #  #  #  #  #  #  #  #  #  #  #  #  #  #  #
#
#   1)Copier le fichier 'HeaderCalc.php' dans le dossier 'projeqtor/model/custom/' ------------------- DEPLOYMENT
#   2)Copier le fichier 'resourcePlanWeekly.php' dans le dossier 'projeqtor/report/' ----------------- DEPLOYMENT
#   3)Lance le scripr langscript.py------------------------------------------------------------------- DEPLOYMENT
#
######################## MAJ #######################################################################################
#
#   ./deploy.sh 0 <dossier projeqtorV920>
#
#  #  #  #  #  #  #  #  #  #  #  #  #  #  #  #  #  #  #  #  #  #  #  #  #  #  #  #
#
#   1) Eteindre le service Apache2.4 ----------------------------------------------------------------- MAJ
#   2) Copier le fichier 'projeqtor/files/config/parameters.php' dans le dossier courrante ----------- MAJ
#   3) Effacer toute l'arborescence projeqtor -------------------------------------------------------- MAJ
#   4) Copier la nouvelle arborescence --------------------------------------------------------------- MAJ
#   5) Copier le fichier 'parameters.php' dans 'projeqtor/files/config/' ----------------------------- MAJ
#   6) Copier le fichier 'HeaderCalc.php' dans le dossier 'projeqtor/model/custom/' ------------------ MAJ
#   7) Copier le fichier 'resourcePlanWeekly.php' dans le dossier 'projeqtor/report/' ---------------- MAJ
#   8) Lancement script langscript.py ---------------------------------------------------------------- MAJ   
#   9) Start les services Apache2.4 ------------------------------------------------------------------ MAJ
#
####################################################################################################################
############################# A FAIRE DANS LES DEUX CAS ############################################################
#   
#   FINAL) Dans l'interface MySQL de la base des données, lancer les requêtes du fichier 'parametersDeploiement.php' dans le serveur ------------ MAJ & DEPLOYMENT
#
#   ############################################################################################################   #

echo $#
if [ $# -eq 1 ]; then
    echo "entered"
    echo "Peu d'arguments utiliser correctement la commande :"
    echo "'deploy.sh 1 0' pour deploiement ou 'deploy.sh 0 <dossier projeqtorVXXX>' pour mise à jour projeqtor"
elif [ $# -eq 2 ]; then
    if [ $1 -eq 1 ]; then
        echo "DEPLOYEMENT"

        #1
        mv HeaderCalc.php projeqtor/model/custom/

        #2
        mv resourcePlanWeekly.php projeqtor/report/
        echo "."

        #3
        py langscript.py

        echo "."
        echo "DEPLOYMENT DONE"
    elif [ $1 -eq 0 ]; then
        echo "MAJ"
        #1
        Net stop Apache2.4
        
        #2
        cp projeqtor/files/config/parameters.php ./
        echo "."

        #3 efface arbo projeqtor
        rm -r projeqtor

        #4 move new arbo
        mv $2/projeqtor/ ./

        echo "."
        #5
        mv parameters.php  projeqtor/files/config/

        #6
        mv HeaderCalc.php projeqtor/model/custom/
        echo "."
        #7
        mv resourcePlanWeekly.php projeqtor/report/
        
        #8
        py langscript.py
        
        #9
        Net start Apache2.4
        echo "."
        echo "MAJ DONE"
    else
        echo "Arguments incorrects, utiliser :"
        echo "'deploy.sh 1 0' pour deploiement ou 'deploy.sh 0 <dossier projeqtorVXXX>' pour mise à jour projeqtor"
    fi
else   
    echo "Trop d'arguments"
    echo "'deploy.sh 1 0' pour deploiement ou 'deploy.sh 0 <dossier projeqtorVXXX>' pour mise à jour projeqtor"

fi
