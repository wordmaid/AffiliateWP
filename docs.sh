#!/bin/bash
# AffiliateWP Developer Documentation generator
# If you receive permissions errors, run: chmod 755 docs.sh


# AffiliateWP docs generator variables
AFFWP_VERSION=$(grep " * Version: " affiliate-wp.php)
AFFWP_DOCS_VERSION="0.1"
AFFWP_DOCS_BASE_URL="https://docs.affiliatewp.com/collection/developers"

# A general-purpose ask function
ask() {
   while true; do

		if [ "${2:-}" = "Y" ]; then
		   prompt="Y/n"
		   default=Y
		elif [ "${2:-}" = "N" ]; then
		   prompt="y/N"
		   default=N
		else
		   prompt="y/n"
		   default=
		fi

		# Ask the question
		echo -n "$1 [$prompt] "

		# Read answer
		read REPLY </dev/tty

		# Default?
		if [ -z "$REPLY" ]; then
		   REPLY=$default
		fi

		# Check if reply is valid
		case "$REPLY" in
		   Y*|y*) return 0 ;;
		   N*|n*) return 1 ;;
		esac

   done
}

# Generates markdown files:
#  1. Check if apigen is installed. If not, it installs apigen.
#  2. Runs apigen, and outputs to the affwp-plugin-dir/docs directory.
affwp_docs_generate_html() {
	# Check if apigen is installed
	if ! type "apigen" > /dev/null; then
		# Install apigen if unavailable.
		echo "Installing APIGEN library..."
		wget http://apigen.org/apigen.phar
		chmod +x apigen.phar
		mv apigen.phar /usr/local/bin/apigen
		apigen_version=apigen --version
		echo "Installed" apigen_version " successfully."
	fi

	# Run if apigen is installed
	if type "apigen" > /dev/null; then
		# Create /docs directory
		echo "Creating /docs directory..."
		mkdir docs && echo "Done."
		echo "Generating docs...";
		apigen --quiet
	fi
}

# Convert our html to markdown files
affwp_docs_convert_to_markdown() {

	echo "Converting output to markdown..."

	# Check if pandoc is installed
	if ! type "pandoc" > /dev/null; then
		# Install pandoc if unavailable.
		echo "Installing pandoc library..."
		brew install pandoc && echo "Done."
	fi

	# Run if pandoc is installed
	if type "pandoc" > /dev/null; then
		# Convert to md
		echo "Beginning markdown conversion"

		# Loop through each .html file in the /docs directory
		for i in docs/*
		do
			pandoc $i -f html -t markdown -s
		done

		echo "Markdown conversion complete."
	fi
}

affwp_docs_delete_html() {
	echo "Deleting html files..."
	#rm -rf docs/*.html ** echo "Done."
	find . -name "*.html" -type f -delete

}

# Upload docs to Helpscout.
#
# TODO, still working on this one.
#
affwp_docs_helpscout() {
	# Prompt user for their Helpscout API key.
	# Employees only.
	echo -n "Please provide your Helpscout API key. >"
	read HS_API_KEY
	echo "Connecting to Helpscout Docs API..."
	curl --user $HS_API_KEY:X https://docsapi.helpscout.net/v1/collections

}

# Commit files to the AffiliateWP/AffiliateWP Github repo.
# Please note that documentation should typically only
# be re-generated for a release.
affwp_docs_commit() {
	echo "You'll need to commit yourself for now, as this feature is not complete."
}

# Primary AffiliateWP docs generator.

affwp_docs() {

	affwp_docs_generate_html &&
	affwp_docs_convert_to_markdown &&
	affwp_docs_delete_html &&
	affwp_docs_helpscout &&
	affwp_docs_commit &&
	echo "Docs complete for AffiliateWP "  $AFFWP_VERSION
}

# Check if figlet installed
if ! type "figlet" > /dev/null; then
  	# Install figlet if unavailable
  	echo "Installing dependencies..."
  	brew install figlet && "Done."
fi

# Greeting
if type "figlet" > /dev/null; then
	# break
	printf "\n"
	figlet -l -p -f big "AFFWP DOCS"
	echo "(version" $AFFWP_DOCS_VERSION ")"
else
	echo "AFFWP Docs Generator (version" AFFWP_DOCS_VERSION ")"
fi

# Default to No if user presses enter without giving an answer:
if ask "This script will generate AffiliateWP developer documentation in AffiliateWP/docs, and optionally upload changes to Helpscout. Continue?" N; then
   echo "Great! Grab some coffee, this might take a minute or two."

   # Run the primary AffiliateWP docs generator.
   affwp_docs
else
    echo "Ok bye."
fi
