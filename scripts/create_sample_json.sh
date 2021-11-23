sample_dir='/home/zenity/zenity/snapshots/test'
statspackReading="/home/zenity/zenity/parsing/statspackReading.py" 
rm $sample_dir/*.json
for statspack in $sample_dir/*.txt
do
	echo $statspack
	python $statspackReading '' $statspack >> $statspack.json
done

