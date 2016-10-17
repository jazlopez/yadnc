# YADNC - Yet Another Domain Name Checker

## Purpose
Basic implementation containing a Domain Name Checker class along a test implementation example. 

The goal of Domain Name Checker class is to run domain availability lookup on <b>.com, .org, .net, .info, .biz, .mx </b> extensions.

## Usage

1. You may want to clone the repository and run an instance of PHP web built-in server

2. Make sure to send a `name` variable either in a GET/POST request when calling in the script.

### Clone the code 

```
# clone the code
$git clone https://github.com/jazlopez/yadnc.git
```

### Test

```
# run a built in web server
$php -S localhost:8085 -t yadnc
```

### Verify
Open up a brower and type in http://localhost:8085/domain-name-checker?name=domain-without-extension


## Response

It returns a json array indicating extension avaialibity for the requested name.

```
{"response":["domain-without-extension.com is available","domain-without-extension.net is available","domain-without-extension.org is available","domain-without-extension.us is available","domain-without-extension.biz is available","domain-without-extension.info is available"]}
```

## Exception

In the case of any error it throws a 400 Exception containing the error description.

```

{"error":"Domain name cannot be empty, \/Users\/me\/yadnc\/domain-name-checker.php:185"}

```

## Contact Support

Jaziel Lopez

Experienced Software Developer, 

Tijuana Area, Mexico, 2016

<a target="_blank" href="mailto: juan.jaziel@gmail.com">juan.jaziel@gmail.com</a> | <a target="_blank" href="http://jlopez.mx">http://jlopez.mx</a> | <a target="_blank" href="http://linkedin.com/in/jlopezmx">http://linkedin.com/in/jlopezmx</a>

> Improvements and recommendations are well appreciated. Feel free to fork and use.
