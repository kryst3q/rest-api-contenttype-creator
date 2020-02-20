# REST API contenttype creator

## Description
Bolt 3 extension allowing contenttypes creation using REST API request. It also let sending email when it occurs. It can be helpful while using the extension as contact form or job offer application form to inform employee about new contact tries or job applications.

## Configuration

```
# prefix of this extensions rest api routings
api_prefix: 'api'
# list of content types that will be handled by extension
content_type:
    # content type name identical to one from contenttypes.yml file
    entries:
        # information if given content type has to be send through email after saving
        send_email: false
        # content type fields that will be used as email message
        #message_fields: [title, body]
        # method of concatenating message fields into one message; default "\n"
        #implode_glue: \n
        # email configuration that should be used for sending given content type; default will be used if not specified
        #email_configuration_name: default
        # name of email sender data from "sender" list; default will be used if not specified
        #sender_name: default
        # name of email receiver data from "receiver" list; default will be used if not specified
        #receiver_name: default
        # name of message metadata from "message" list; default will be used if not specified
        #message_name: default
# list of available email configurations
email_configuration:
    # name of given email configuration
    default:
        host: localhost
        port: 25
        security: null # possible values: null, ssl, ttl
        username: null
        password: null
# list of available email senders
sender:
    # name of given email sender data configuration
    default:
        name: Dolor Sit
        email: dolor@sit.com
# list of available email receivers
receiver:
    # name of given email receiver data configuration
    default:
        name: Lorem Ipsum
        email: lorem@ipsum.com
# list of available message configurations
message:
    # name of given message configuration
    default:
        # subject of email message
        subject: New contact form
        # path to email template; if set to null the default one will be used
        template: null
```

## Examples

Assume that we want to have contact form in one of our sites and want to be informed by email when someone will fill one.
In that situation we create new contenttype in contenttypes.yml file that will be representing our contact form:

```
contact_forms:
    name: Contact forms
    singular_name: Contact form
    fields:
        name:
            label: First name and last name
            type: text
            readonly: true
            group: main
        email:
            label: E-mail address
            type: text
            readonly: true
            group: main
        message:
            label: Message
            type: text
            readonly: true
            group: main
            required: true
```

Next, if we want to have possibility to create our new contenttype using REST API request we must enable it in extension config so it should looks like below:

```
api_prefix: '/api'
content_type:
    contact_forms:
        send_email: true
        message_fields: [message]
email_configuration:
    default:
        host: some.mail-host.net
        port: 465
        security: ssl
        username: SomeUsername
        password: somepassword
sender:
    default:
        name: Bolt
        email: someone@somewhere.net
receiver:
    default:
        name: Lorem Ipsum
        email: lorem@ipsum.com
message:
    default:
        subject: New contact attempt
        template: null
```

In `content_type` section we add our `contact_forms` contenttype, enabled sending emails on it's creation and selecting that email content should be ceated from contenttype's `message` field.
That's it. Now it's time to create new contact form:

```
curl -X POST https://mydomain.com/api/create/contact_forms -d '{"name": "Lorem Ipsum", "email": "lorem@ipsum.pl", "message": "Pellentesque id libero sed ipsum vehicula blandit. Integer non lorem imperdiet, dignissim neque in, dignissim risus."}' -H "Content-Type: application/json"
```

The request consist of `/{api_prefix}/create/{contenttype}`.

## TODO
* cover code by tests
* add handling another than SMTP form of email transport
* add validating configuration during container creation (use symfony validator)
* handle case when email was not sent
* add translations
* add using different than JSON body types (yaml, xml) using symfony serializer
* add queueing messages and send them by cron task 
* add handling rest of contenttypes field types
* add file upload
* add using twig template in email message
