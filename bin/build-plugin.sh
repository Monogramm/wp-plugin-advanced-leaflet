#!/bin/bash

printf "Plugin name: "
read NAME

printf "Destination folder: "
read FOLDER

printf "Include Grunt support (Y/n): "
read GRUNT

printf "Initialise new git repo (y/n): "
read NEWREPO

DEFAULT_NAME="WP Plugin Template"
DEFAULT_CLASS=${DEFAULT_NAME// /_}
DEFAULT_TOKEN=$( tr '[A-Z]' '[a-z]' <<< "$DEFAULT_CLASS")
DEFAULT_SLUG=${DEFAULT_TOKEN//_/-}

CLASS=${NAME// /_}
TOKEN=$( tr '[A-Z]' '[a-z]' <<< "$CLASS")
SLUG=${TOKEN//_/-}

git clone "https://github.com/Monogramm/$DEFAULT_SLUG.git" "$FOLDER/$SLUG"

echo "Removing git files..."

mkdir -p "$FOLDER"
cd "$FOLDER/$SLUG"

rm -rf .git
rm bin/build-plugin.sh

if [ "$GRUNT" == "n" ]; then
	rm Gruntfile.js
	rm package.json
fi

echo "Updating plugin files..."

mv "$DEFAULT_SLUG.php" "$SLUG.php"

mv "lang/$DEFAULT_SLUG.pot" "lang/$SLUG.pot"

mv "includes/class-$DEFAULT_SLUG.php" "includes/class-$SLUG.php"
mv "includes/class-$DEFAULT_SLUG-settings.php" "includes/class-$SLUG-settings.php"

mv "includes/lib/class-$DEFAULT_SLUG-post-type.php" "includes/lib/class-$SLUG-post-type.php"
mv "includes/lib/class-$DEFAULT_SLUG-taxonomy.php" "includes/lib/class-$SLUG-taxonomy.php"
mv "includes/lib/class-$DEFAULT_SLUG-admin-api.php" "includes/lib/class-$SLUG-admin-api.php"

mv "tests/$DEFAULT_CLASS.php" "tests/$CLASS.php"

for f in README.md readme.txt 'uninstall.php' "$SLUG.php" admin/js/*.js "lang/$SLUG.pot" "includes/class-$SLUG.php" "includes/class-$SLUG-settings.php" "includes/lib/class-$SLUG-post-type.php" "includes/lib/class-$SLUG-taxonomy.php" "includes/lib/class-$SLUG-admin-api.php" "tests/$CLASS.php" '.env' '.travis.yml' '.phpcs.xml.dist' '.github/PULL_REQUEST_TEMPLATE.md' '.gitlab/merge_request_templates/merge_request_template.md'
do
	cp "$f" "$f.tmp"
	sed "s/$DEFAULT_NAME/$NAME/g" "$f.tmp" > "$f"
	rm "$f.tmp"

	cp "$f" "$f.tmp"
	sed "s/$DEFAULT_SLUG/$SLUG/g" "$f.tmp" > "$f"
	rm "$f.tmp"

	cp "$f" "$f.tmp"
	sed "s/$DEFAULT_TOKEN/$TOKEN/g" "$f.tmp" > "$f"
	rm "$f.tmp"

	cp "$f" "$f.tmp"
	sed "s/$DEFAULT_CLASS/$CLASS/g" "$f.tmp" > "$f"
	rm "$f.tmp"
done

if [ "$NEWREPO" == "y" ]; then
	echo "Initialising new git repo..."
	git init
fi

echo "Complete!"
