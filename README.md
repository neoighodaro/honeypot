##Honeypot v2 Security Module For Kohana Framework

####Changes from 1.0 to 2.0
 [*] Restructured the whole Honeypot class and added support for drivers.
 [*] Force CSRF check as part of the Honeypot module.

###Introduction
This is a Security Module for the [Kohana Framework](http://kohanaframework.org). Its a good replacement for Captcha.
To install this module, download the zip, and then unzip into the Kohana modules directory.

Activate the module by adding it to your <code>application/bootstrap.php</code> file.

To use it, add it to a form in your views with:

	echo Honeypot::make()

Then in your controller you can use this with the Validation library, or as a standalone.

	// Stand alone without CSRF check
	if ( ! Honeypot::check())
	{
		// not valid
	}

Notice an error, please use the issue tracker, or fork, fix and submit a pull request.
