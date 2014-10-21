OpenVBX Plugin Recording
========================

A Plugin for OpenVBX that adds Recording features.

# Requirements

Tested for use with OpenVBX 1.2.14
It should also work with version 1.2.13.
If you find any issues please open a [support request] [1].

[1]: https://github.com/kernworks/OpenVBX-Plugin-Recording/archive/master.zip

# Installation

[Download][2] the plugin and extract to /plugins

[2]: https://github.com/kernworks/OpenVBX-Plugin-Recording/issues

## Usage

Once installed, New applets will appear on the sidebar when you create flows. You will also see a new page appear on the sidebar.

### Applets

#### Dial Record

This works exactly the same way as the standard Dial applet with a few additional features:

* You have the option of telling the caller something before the dial happens. This is useful for "call may be recorded" messages.
* You can record the dial as it is ringing or when it is answered. It is recommended that you wait until the call is answered.

### Pages

####Recorded Calls

This page allows you to listen to calls that were recorded.

There are several things to be aware of when using this page:

* Accounts with large numbers of recordings may take a while to completely load the page.
  This is due to the limitations of the Twilip API.
* Any new recordings will show up within 5 minutes of the call ending.
* Outbound call recordings do not currently display.

### Future Development

Watch the [develop branch] [3] if you are interested in testing new features.
If you really can't wait for a develop build, check the feature branches for alpha versions. These are not guaranteed to work properly.

[3]: https://github.com/kernworks/OpenVBX-Plugin-Recording/tree/develop

#### Planned Features

- [ ] Allow deletion of a recorded call.
- [ ] Allow customization of wisper when using the Dial Record Applet.

