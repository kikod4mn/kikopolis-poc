# Kikopolis MVC Framework

### An MVC pattern framework with custom templating engine, orm, and basically everything albeit much of it is based in the examples of my lessons from other frameworks and tutorials.

#

This is a learning project for me and there are lots of @todo's in the back of my mind. Currently not much is functional.
Here is the list of current most important functionality.

-   Composer autoloading
-   Ioc container - a simple container that injects dependencies
-   Router - accepts dynamic routes aswell as static ones. Also several helpful functions to add a multitude of different routes and process them. Much work yet to do.
-   Basics of an App kernel are there. Again, lots and lots of @todo's...

Thats it! It's basic and in no way, shape or form is it either production ready and it might not be that ready for a long long while.
But what better way to learn some important concepts in programming than to bite off a way bigger chuck than a beginner can chew? sign...

### Webpack configuration
-
#### 1. Compilation
Simply run "npm run watch" to automatically watch the base css and js files in "src" folder for changes and automatic compiling.
#### 2. Production settings
In the file "postcss.config.js" uncomment the line that simply says "purgecss". This will purge all unused css styles. Then simply run "npm run production" to build the files for prod environment.