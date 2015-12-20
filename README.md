# Private Grav Plugin

`Private` is a [Grav](http://github.com/getgrav/grav) Plugin. (Need help for a best english documentation. )

`Private` provides an authentication form to keep your whole Grav site or just part of it private.

![Private](assets/readme.jpg)

# Installation

Installing the Private plugin can be done in one of two ways. Our GPM (Grav Package Manager) installation method enables you to quickly and easily install the plugin with a simple terminal command, while the manual method is done using a zip file. 

## GPM Installation (Preferred)

The simplest way to install this plugin is via the [Grav Package Manager (GPM)](http://learn.getgrav.org/advanced/grav-gpm) through your system's Terminal (also called the command line).  From the root of your Grav install type:

    bin/gpm install private

This will install the Private plugin into your `/user/plugins` directory within Grav. Its files can be found under `/your/site/grav/user/plugins/private`.

## Manual Installation

To install this plugin, just download the zip version of this repository and unzip it under `/your/site/grav/user/plugins`. Then, rename the folder to `private`. You can find these files either on [GitHub](https://github.com/diyzzuf/grav-plugin-private) or via [GetGrav.org](http://getgrav.org/downloads/plugins#extras).

You should now have all the plugin files under

    /your/site/grav/user/plugins/private


# Usage

By default, the password is `password`, username is **not** needed and your whole Grav site is **fully private**.

To customize these parameters (and more), create an override config file. Create the folder `user/config/plugins` (if it doesn't exist already) and copy the [private.yaml](private.yaml) config file in there and then make your edits.

## Recommended Todos (See Options Section)
1. **Change** the default password in your `user/config/plugins/private.yaml`
2. **Change** the default security salt in your `user/config/plugins/private.yaml`
3. **Customize** your privacy rules

# Options

###### Plugin
Enable or Disable the entire plugin (default: `true`).

    enabled: (true|false)

###### Routing
Routes for login and logout. You can customize these by replacing the value (e.g: login: "/admin" for "mywebsite.com/admin" )

    routes:
        login: "/login"
        logout: "/logout"

###### Security Salt
Security Salt for sessions. You can use this [generator](http://www.sethcardoza.com/tools/random-password-generator/) to generate your own.

    session_ss: random_value

###### Private Site
If set to `true`, the entire site is private. If false, privacy is enabled only on pages tagged with the `private_tag` ( See after ) (default: `true`)

    private_site: (true|false)
    
###### Private Tag
Identification of privates pages if `private_site` is `false`. You **must** add the private_tag the your private pages. ( default: `hidden`)
See [Grav Taxonomy](http://learn.getgrav.org/content/taxonomy) for more information.

    private_tag: hidden

###### Username on login page
Enable (`true`) or Disable (`false`) username on the login page. (default: `false`)
> Note : If you disable the username, you need to **keep** `no_user` username on users parameters
    enable_username: (true|false)

###### Users list
List of users. To add a user, just create a new line **keeping the identation**. (default password: `password`)
> Note : If enable_username is `false`, the user **MUST BE KEEPING** on `no_user`

>> Note : The password **MUST BE** use SHA1. See [SHA1 Online](http://www.sha1-online.com) to generate a SHA1 password.

    users:
        no_user : sha1_password

###### Localized Text
You can localize the labels and text of the inputs into your own language.

    fields:
        username:
            label: "Username"
            placeholder: "Enter your username"

# Updating

As development for the Private plugin continues, new versions may become available that add additional features and functionality, improve compatibility with newer Grav releases, and generally provide a better user experience. Updating Private is easy, and can be done through Grav's GPM system, as well as manually.

## GPM Update (Preferred)

The simplest way to update this plugin is via the [Grav Package Manager (GPM)](http://learn.getgrav.org/advanced/grav-gpm). Navigate to the root directory of your Grav install using your system's Terminal (also called command line) and typing the following:

bin/gpm update private

This command checks your Grav install to see if an update is available for your version of Private. If a newer release is found, you will be asked whether or not you wish to update. To continue, type `y` and hit enter. The plugin will automatically update and clear Grav's cache.

## Manual Update

Manually updating Private is pretty simple.

* Delete the `your/site/user/plugins/private` directory.
* Download the new version of the Private plugin from either [GitHub](https://github.com/diyzzuf/grav-plugin-private) or [GetGrav.org](http://getgrav.org/downloads/plugins#extras).
* Unzip the zip file in `your/site/user/plugins` and rename the resulting folder to `private`.
* Clear the Grav cache. The simplest way to do this is by going to the root Grav directory in terminal and typing `bin/grav clear-cache`.

> Note: Any changes you have made to any of the files listed under this directory will also be removed and replaced by the new set. Any files located elsewhere (for example a YAML settings file placed in `user/config/plugins`) will remain intact.
