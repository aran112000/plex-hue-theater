# Philips Hue / Plex PHP
Ever wished your Philips Hue lights could be easily adjusted when you start a Movie or Show on Plex?

Well wonder no more, you can now connect your Hue lights in any way you like to Plex so as soon as you start watching, your lights change to your preset values, then reset after you finish.

**As an example of what's possible, here's an example of how we've used this:**

When **my** Plex user starts start Casting media to a specific Chromecast, the various Hue lights in that room adjust to pre-defined brightnesses and colours subject to the type of media *(darker for Movies than TV shows)*.

Different rooms do different things, and if it's not dark outside, then no changes will happen to my lights.

**Sound like something you'd like to have a play around with yourself?**

Great, keep reading and I'll walk you through how you can be up and running in just a few minutes!

## Requirements
- PHP 7.4+ webserver *(running on the same local network as your Philips Hue bridge)*

- Plex Pass subscription (needed for webhook support)

## Setup
1. **Plex webhook setup**

    Create a new Webhook within Plex Server, and point its URL to your hosted URL for `/webhook.php` (this does work with localhost URLs if your PHP application server is on the same device as your Plex Server).
    
    **IMPORTANT NOTE:** It's not documented, but I discovered I needed to restart my local Plex Server after adding/changing a webhook for it to take affect.
    
2. **Setup your Hue credentials**

    Copy `settings.ini.example` --> `settings.ini` and follow the instructions at the top of the file to get your Hue API details
    
3. **Find your Hue bulbs**

    Start your PHP server and browse to `http://localhost/myLights.php` (or your equivalent URL for `/myLights.php`, assuming everything before was setup correctly, this will list all your Hue lights. **Note the IDs, you'll need these for the next step.**
    
    *Should you wish, `myLights.php` can be deleted after you've completed this step, it's only there to help you find the IDs for your relevant Hue devices.*
    
4. **Reference your devices**
    
    Within `/src/Device.php`, define friendly constants for the lights you want to control (helps keep your code clean). The IDs associated with your constants should be those reported by the last step above.
    
    *Feel free to remove the default example devices within there and replace them with your own and name them as you desire, you'll reference your device names in the next step.*
    
5. **Setup what should happen when you play/stop media on Plex** 

    Within `src/PlexWebhookHandler.php`, you'll find `play()` and `stop()` methods where you can define what actions you wish to take on those Plex events. This is pre-populated with an example from my own setup, so you'll need to update the devices to use the constants which you just defined above in step 4. 

### Helpful things to know about Plex's webhooks:
* At the time of writing, Plex's webhook support requires you to have a Plex Pass subscription

* localhost / 127.0.0.1 URLs are fine if your PHP application server and Plex Server are running from the same device

* At the time of writing, Plex's webhooks don't require HTTPS, so **no** need to worry about issuing an SSL certificate

