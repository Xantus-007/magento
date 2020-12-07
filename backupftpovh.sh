#!/bin/bash

#***********************************************************************#
#             BACKUP SUR FTP OVH  par daniel Polli aka Dansteph         #
#             ------------------                                        #
# Ce script a lancer en cron tout les deux ou trois jours backup les    #
# repertoire "/home" "/usr/local/apache/conf/" et "/var/named"          #
# et les envois sur votre espace backup FTP. Editez les paramètres      #
# ci-dessous.                                                           #
#                                                                       #
#***********************************************************************#

#########################################################################
# PARAMETRES A EDITER 
#########################################################################
SERVER="ftpback-rbx3-220.ovh.net"		    	#Serveur backup d'OVH
USER="ns213538.ovh.net"	            		#Votre nom d'utilisateur
PASS="7VWtwMPdAF3"			    	#Votre password
EMAIL="nicolas@altiplano.fr"        		#Pour envoi mail si backup echoue
MAILSIOK="O"			    	#Mettre "O" si on veut un mail aussi si backup ok
SAVEDIR="/home/backuppc/pc" 			#Backup de /home SANS slash a la fin
SAVEDIR1=""	#Backup optionnel SANS slash a la fin
SAVEDIR2=""		        #Backup optionnel SANS slash a la fin
DATETIME=`date "+%Y-%m-%d_%H-%M-%S"`
#########################################################################

#autres parametre a éditer seulement par confirmé------------------------
FILENAME="monbento_"$DATETIME"_backupppc.tar"		    #nom du fichier "[jour]_backupsite.tar.gz" (.gz est ajouté après)
TEMPDIR="/home/backuppc/"                         #repertoire temporaire de home pour creation tar
EXCLUDEFILE="/home/backuppc/backup_exclude.txt"   #ce fichier doit contenir les rep a exclure du backup
#fin parametres, rien pour vous plus bas---------------------------------

STARTTIME=`date +%s`

#pour traduire les codes d'erreur de ncftpput en texte
declare -a CDERR
CDERR[1]="Could not connect to remote host."
CDERR[2]="Could not connect to remote host - timed out."
CDERR[3]="Transfer failed."
CDERR[4]="Transfer failed - timed out."
CDERR[5]="Directory change failed."
CDERR[6]="Directory change failed - timed out."
CDERR[7]="Malformed URL."
CDERR[8]="Usage error."
CDERR[9]="Error in login configuration file."
CDERR[10]="Library initialization failed."
CDERR[11]="Session initialization failed."
CDERR[142]="Delai depassé pour la connexion."

#envoi des infos sur le log authpriv (le log "secure" sur OVH)
DATE=`date +%H:%M:%S`
logger -p authpriv.info "[$0] -->Debut de backup de $SAVEDIR a $DATE"
echo "[$0] -->Debut de backup de $SAVEDIR a $DATE"

find $SAVEDIR -type f -mtime 0 | xargs tar -rf $TEMPDIR$FILENAME
gzip $TEMPDIR$FILENAME
RESULT=$?
if [ "$RESULT" != "0" ]; then
        DATE=`date +%H:%M:%S`
        logger -p authpriv.info "[$0] -->ERREUR TAR à $DATE Backup NON effectué."
        echo "[$0] -->ERREUR TAR à $DATE Backup NON effectué."
	echo "Erreur TAR le backup FTP sur OVH non effectue" | mail -s 'ERREUR BACKUP FTP OVH' $EMAIL
	exit $RESULT
fi

ncftpput -m -u $USER -p $PASS $SERVER "/" $TEMPDIR$FILENAME.gz
RESULT=$?
FILESIZE=`ls -l $TEMPDIR$FILENAME.gz | awk '{print $5}'`
FILESIZE=$(($FILESIZE/1000000))
rm -f $TEMPDIR$FILENAME.gz
if [ "$RESULT" != "0" ]; then
	DATE=`date +%H:%M:%S`
	logger -p authpriv.info "[$0] -->ERREUR: ${CDERR[$RESULT]} à $DATE Backup NON effectué."
	echo "[$0] -->ERREUR: ${CDERR[$RESULT]} à $DATE Backup NON effectué."
	echo "[$0] -->ERREUR: ${CDERR[$RESULT]} à $DATE Backup NON effectué." | mail -s 'ERREUR BACKUP FTP OVH' $EMAIL
else
	TOTALTIME=$(((`date +%s`-$STARTTIME)/60))
	DATE=`date +%H:%M:%S`
	logger -p authpriv.info "[$0] -->Fin de backup normal de $SAVEDIR a $DATE. Durée: $TOTALTIME mn. Taille: $FILESIZE Mb"
	echo "[$0] -->Fin de backup normal de $SAVEDIR a $DATE.  Durée: $TOTALTIME mn. Taille: $FILESIZE Mb"

	if [ "$MAILSIOK" = "O" ]; then
		echo -e "Backup effectué à $DATE Status: OK\nDurée du backup: $TOTALTIME minutes\nFichier: $FILENAME.gz transféré avec une taille de $FILESIZE Mb" | mail -s 'BACKUP FTP OVH OK' $EMAIL
        fi
fi
exit $RESULT
