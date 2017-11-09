# Private Grav Plugin

> New version is [Private Site](https://github.com/Diyzzuf/grav-plugin-private-site) **(beta stage, not for production. Need users to test this plugin)**

`Private` is a [Grav](http://github.com/getgrav/grav) Plugin.

Private provides an authentication form to keep your entire Grav site or part of it private from the general public.

![Private](assets/readme.jpg)

# Installation

Installing the Private plugin can be done in one of two ways. The GPM (Grav Package Manager) installation method enables you to quickly and easily install the plugin with a simple terminal command, while the manual method allows you to do so by downloading a zip file to place in the Grav plugins directory. 

## GPM Installation (Preferred)

The simplest way to install this plugin is via the [Grav Package Manager (GPM)](http://learn.getgrav.org/advanced/grav-gpm) through your system's Terminal (also called the command line).  From the root of your Grav install type:

    bin/gpm install private

This will install the Private plugin into your `/user/plugins` directory within Grav. Its files can be found under `/your/site/grav/user/plugins/private`.

## Manual Installation

To install this plugin, just download the zip version of this repository and unzip it under `/your/site/grav/user/plugins`. Then, rename the folder to `private`. You can find these files either on [GitHub](https://github.com/diyzzuf/grav-plugin-private) or via [GetGrav.org](http://getgrav.org/downloads/plugins#extras).

You should now have all the plugin files under

    /your/site/grav/user/plugins/private


# Usage

By default, the password is `password`, username is **not** needed and Grav is **fully private**.

To customize this parameters (and more), you first need to create an override config. To do so, create the folder `user/config/plugins` (if it doesn't exist already) and copy the [private.yaml](private.yaml) config file in there and then make your edits.

## Recommended changes to be made to ensure your site is secure (See Options section below)
1. **Change** the default password in your `user/config/plugins/private.yaml`
2. **Change** the default security salt in your `user/config/plugins/private.yaml`
3. **Customize** your privacy rules

# Options

###### Plugin
Enable or Disable the entire plugin (default: `true`).

    enabled: (true|false)

###### Routing
Routes of login and logout. You can customize it by replacing value (e.g: login: "/admin" for "mywebsite.com/admin" )

    routes:
        login: "/login"
        logout: "/logout"

###### Security Salt
Security Salt for session. **IT MUST BE AN ALPHANUMERIC CHAR** You can go to this [generator](http://www.sethcardoza.com/tools/random-password-generator/) for your own. [temporary cached version](http://webcache.googleusercontent.com/search?q=cache:www.sethcardoza.com/tools/random-password-generator/)

    session_ss: random_value

###### Private Site
If `true`, the entire site is private. If false, then Private can be enabled on a page by page basis by using the `private_tag` ( See after ) (default: `true`)

    private_site: (true|false)
    
###### Private Tag
If the private_site value is `false`, you will need to add the `private_tag` on your private page. (default: `hidden`)
See [Grav Taxonomy](http://learn.getgrav.org/content/taxonomy) for more information.

    private_tag: hidden

###### Username on login page
Enable (`true`) or Disable (`false`) the username field on the private page's login form. (default: `false`)
> Note : If you disable the username, you need to **keep** `no_user` username in the `users` parameters.
    enable_username: (true|false)

###### Users list
List of users. For adding user, just create a new line **keeping the identation**. (default password: `password`)
> Note : If enable_username is `false`, you must not delete the `no_user` user in the list.

>> Note : The password **MUST BE** a SHA1 value. For quick checking see [SHA1 Online](http://www.sha1-online.com) to generate your SHA1 password. But it is adwised to generate it locally as http and any transmission is not as secure as no transmission at all. Use command: ` echo -n "yourpassword" | sha1sum`. You can remove it thereafter from bash history with `history -d 1234` and sourcing the ~/bashrc.

    users:
        no_user : sha1_password

###### Text
This section allows you to change the text which will appear on the login form on Private enabled pages.

    fields:
        username:
            label: "Username"
            placeholder: "Enter your username"

# Updating

As development for the Private plugin continues, new versions may become available that add additional features and functionality, improve compatibility with newer Grav releases, and generally provide a better user experience. Updating Private is easy, and can be done through Grav's GPM system, as well as manually.

## GPM Update (Preferred)

The simplest way to update this plugin is via the [Grav Package Manager (GPM)](http://learn.getgrav.org/advanced/grav-gpm). You can do this by navigating to the root directory of your Grav install using your system's Terminal (also called command line) and typing the following:

bin/gpm update private

This command will check your Grav install to see if your Private plugin is due for an update. If a newer release is found, you will be asked whether or not you wish to update. To continue, type `y` and hit enter. The plugin will automatically update and clear Grav's cache.

## Manual Update

Manually updating Private is pretty simple. Here is what you will need to do to get this done:

* Delete the `your/site/user/plugins/private` directory.
* Download the new version of the Private plugin from either [GitHub](https://github.com/diyzzuf/grav-plugin-private) or [GetGrav.org](http://getgrav.org/downloads/plugins#extras).
* Unzip the zip file in `your/site/user/plugins` and rename the resulting folder to `private`.
* Clear the Grav cache. The simplest way to do this is by going to the root Grav directory in terminal and typing `bin/grav clear-cache`.

> Note: Any changes you have made to any of the files listed under this directory will also be removed and replaced by the new set. Any files located elsewhere (for example a YAML settings file placed in `user/config/plugins`) will remain intact.
