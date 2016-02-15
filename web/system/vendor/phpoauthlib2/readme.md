# phpoauthlib2

phpoauthlib2 is another OAuth 2.0 library for PHP. The goal of the project is to make it as easy as possible to integrate OAuth into your web application.

You can think of phpoauthlib2 as a combination of ideas from the following projects:

- PHPoAuthLib
- li3_socialauth
- oauth-4-laravel
- PHPoAuthUserData

All wrapped in one simple library.

# Why phpoauthlib2?

Or more specifically why "2"?

For me this is version 2.0. Originally I developed this in private for PHP and it worked but after a refactoring to Django/Python (Python version coming soon to a pip repository near you) then I ported it back to PHP.

# How to use

It's very easy to use this library. Examine the following line -

    $authProvider = new GoogleAuthProvider($_GET, [
        "client_id" => "apps.googleusercontent.com",
        "client_secret" => "<KEY>",
        "redirect_uri" => "http://example.com/phpoauthlib2/example.php"
    ]);
	
client_id and client_secret are provided by the OAauth provider (in this case Google) and the redirect_uri is where you want to the user to end up on successful login. It should go without saying that client_id and client_secret should be kept private - you should avoid committing them a public place like github (yes - people have services running and monitoring for people who commit credentials. Don't believe me? Commit your Amazon AWS keys and see how fast people will spin up VMs). The library will handle the verification and present you with some simple base methods to extract data you might be interested in or the ability to work with the entire OAuth data. 

    $oauth = new OAuth($authProvider, $_GET);
	
OAuth is really a wrapper to do the verification check. In both this line and the previous one we are passing $_GET but phpoauthlib2 can accept any request array from your framework (provided your framework can emit the GET request as an array - which I know at least Symfony can do this).

    $check = $oauth->check();
	
The check method will return true or a string. Not ideal but I couldn't think of any simpler way to do it (obviously not a problem in a lose typed language - but I don't personally like mixing return types). true indicates that the user successfully logged in and you have access to the user's information. A string indicates that you need to redirect them to the OAuth provider to login (the string itself is the redirect URL).

    if ($check === true) {
        echo "Hello - " . $authProvider->getFirstName();
        echo "<br>Your email is - " . $authProvider->getEmail();
    } else {
        header("Location: " . $check);
    }
	
This library is designed to be very minimal - so you need to decide how to hook into the login subsystem of your web application. In the example file - it's checking to make sure that the login was successful and then can call $authProvider->getXXX (such as getFirstName and getEmail in this example) and the provider class will return those fields from the raw profile data so you don't have to worry about it.

The work flow to integrate to your system is usually:

    if ($check === true) {
	    $mySystem->login($authProvider->getEmail()); // which sets a cookie or session that they logged in with this specific user
		header("Location: http://example.com/yoursystem/user.php"); // The line above logs them in to your system - then immediately bounce back to your system and potentially send them straight to their user dashboard

The reasoning behind the getXXX methods is to provide some commonality between providers. That way you can present a OAuth login prompt for different services to the user and you can just call $provider->getEmail() to get their email without having to worry about the actual field that the OAuth provider decided to put it in.

If after you have verified the login was successful you may call

    $provider->getRawProfile();
	
To return the raw return from the OAuth provider (which will be an associative array).

# Google

To get OAuth credentials for Google just go to this URL: https://console.developers.google.com/

And create a project (which is free) and go to APIs & auth -> Credentials.
If you are creating a new project - it may complain that you need to setup the OAuth consent screen. Do this and return to the credentials section and you should be able to setup the project.

Add credentials -> OAuth 2.0 client ID
Then select Web application

It is very important that you input a correct authorized redirect URI. This will be where the user will be sent back on successful login.

# License

I am licensing this under the MIT license. Which essentially grants you the right TDWTFYWWI (to do whatever the f you want with it) - assuming that you acknowledge that I don't provide a warranty.

# What this library is/is not

- This library is a simple interface to use PHP OAuth 2.0 in your web application.
- This library is designed to be as flexible as possible to use in any framework.

- This library is not designed to hold your hand to secure your client_id, client_secret, or other data.
- This library is not designed to be specific to a certain framework.
- This library is not designed to be abstract. The only class you should ever have to extend is OAuthDataProvider - and that is to create a "provider" for different OAuth providers (which merely contains the URLs to send for login, where to query for user data and normalizing data).