sample_dir='/home/zenity/zenity/snapshots/test'
snapshot_dir="/home/zenity/zenity/snapshots-done/"
cd $snapshot_dir
mkdir -p $sample_dir
rm $sample_dir/*
for base in `ls * | grep -oE '[^_]*_([^-]*-){2}' | uniq `
do 
	ls $base* | head -4 | xargs cp -t $sample_dir
done
cd $sample_dir
rename 's/[^_]*_/TEST_/' *
