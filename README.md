# ForeverAlone

ForeverAlone is an open source solution to match people with similar interests.
The theory is that current dating sites have become too vain with an individual's
appearance rather than matching on interests or beliefs.

In today's world of always-on and always-connected I personally feel like we are
drifting further and further away from each other. Have you noticed that many
times when going out to dinner there is a familiar sight of
people buried in their phones rather than talking to each other?

There have been several attempts at this including omegle - which will
attempt to connect to a random person from anywhere in the world. Of course,
these have had various degrees of success (or more simply put - there are
a lot of creepy people on them).

I wanted to create a solution where people can easily find others who share
similar interests and meet a new friend or potential significant other.
Matching shouldn't be done based on some mysterious algorithm saying
that you are 80% match, ABCDEF personality, or how well we can take a selfie.

ForeverAlone creation is not supposed to be a medium for people to find one night
stands or creepy people to troll people. There will eventually be mechanisms put
in place which will allow reporting and if someone is causing problems then
they may me banned or their geolocation based on area may be required to go
through a verification process.

ForeverAlone is not, and never will be, a medium designed to be used for anonymous
and/or untraceable communication. Solutions for that already exist. Your privacy
is respected but you will be reported to the authorities if you do something illegal.

# Security

There is no registration so no concerns about user data. The only data that is
stored is the interests, gender, and other information that a user gives as
a response to the matching questions. That data is only tied to a specific
session and purged after it becomes stale. The session cannot be used to
track to a real person. The session id is a random value for each visitor
that is stored in a cookie. Data in the session is stored server side and
cannot be modified directly by the user.

# Install

1. Copy web/application/config.dist.php to web/application/config.php
2. Edit values for your environment[1]
3. Run `php migrations.php run` to setup your database
6. Navigate to http://example.com/foreveralone and you should be prompted with a set of personal questions

kritbit can run on SQLite - however if you are going to deal with any volume you should use MySQL/MaraiaDB (other databases can be used - but you will need to modify some code).

To use MySQL/MaraiaDB specify in config.php (MariaDB is a drop-in replacement for MySQL so it doesn't matter if you specify MySQL):

    $config["DATABASE_TYPE"] = "MySQL";
    $config['MYSQL_DBNAME'] = "dbname";
    $config['MYSQL_HOST'] = "localhost";
    $config['MYSQL_USER'] = "user";
    $config['MYSQL_PASS'] = "pass";


# Long-term TODO

- Create user registration system
- Create listing of users with similar interests - ala instant messaging vs calculated matching

# Patches

Patches are welcome of any kind. But please do note that your code will be integrated into the project under the MIT license. Mention to your contribution may not appear in the specific code or file that it applies to. But we can certainly make mention on the README describing your contribution.

# Screenshots


# Attributions

ForeverAlone is licensed under the MIT and uses the following projects

- [Haplous Framework](https://srchub.org/p/haplousframework/) - MIT
- [h2o template engine](https://github.com/speedmax/h2o-php) - MIT
- [DByte](https://github.com/Xeoncross/DByte) - MIT
- [stacktraceprint](http://stackoverflow.com/a/4282133/195722)
- [Twitter Bootstrap](http://getbootstrap.com/2.3.2/) - MIT
- [jQuery](https://jquery.com/) - MIT
- [jQuery confirm](http://craftpip.github.io/jquery-confirm/) - MIT
- [is_cli](http://stackoverflow.com/a/25967493/195722)
- [select2](https://select2.github.io/) - MIT
- [waitMe](https://github.com/vadimsva/waitMe) - MIT

# Donations

Donations can be accepted through [Paypal](https://www.paypal.me/NateAdams) or through BTC: [1F3NzZXUm4sATgCs3sgTqXHwrAqM4JnGVS](bitcoin:1F3NzZXUm4sATgCs3sgTqXHwrAqM4JnGVS)

Made with <3 by Nathan Adams
