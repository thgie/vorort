# vorort

This is a simple attempt in making the urban environment read- and writeable.

## how vorort works

vorort generates qr-codes which you print and stick somewhere. Other smartphone users can leave a message on the code when they scan it and follow the link. Every code is unique. So be sure it survives for a while. All messages on a code get lost when the code dies.

Instead of creating a new global online community, vorort facilitates local offline interaction, despite using online technology.

## setup

vorort uses [slim](http://www.slimframework.com) and [paris](http://j4mie.github.com/idiormandparis/) and sqlite. Just deploy this thingy on some webserver and point to ./public