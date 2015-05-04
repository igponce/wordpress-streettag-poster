# wordpress-streettag-poster
Quick way to post map and viewing direction to a wordpress page.

Requires: wordpress-mappost plugin to display map information in the blog

Parts:

- config.php
	Configuration variables: (blog, bloguser, blogpass, google maps api key)
- index.php
	Minimal frontend to start tagging locations.
- mappost.php
	Backend: publish locations and points-of-view to the blog backend using metaweblog API.
