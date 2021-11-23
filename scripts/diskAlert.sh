CURRENT=$(df / | grep / | awk '{ print $5}' | sed 's/%//g')
THRESHOLD=80
if [ "$CURRENT" -gt "$THRESHOLD" ] ; then
   ./send_noreply_mail.sh "[zenity-préprod] Disk Space Alert" "Your root partition remaining free space is critically low. Used: $CURRENT%"
fi

