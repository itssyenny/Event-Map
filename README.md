# Event-Map

## Description
Event Map is a website that can help users to register their account, record their schedule events, and remind users about the upcoming events based on their preferences via email a few days before the upcoming events will be held.

Collaboration of six people in creating this project using frontend (HTML, CSS, Javascript), database (mySQL), and backend (Python). In addition, we also integrated this project with Google API and Google Calendar API.

## Update:
Now supports user preference. Visit https://localhost:8000/preference

Now supports sending email based on user preference. Use `./manage.py notify`.

Now supports editing events. Visit https://localhost:8000/events/edit

## On event types:
Edit `event_map/constants.py` to update event types.


## Prerequisite
* Django
* Everything requried by [Django.gis](https://docs.djangoproject.com/en/2.2/ref/contrib/gis/install/geolibs/)
* In this project we use SQLite, but it's possible to use other databases.
* [Django-map-widgets](https://github.com/erdem/django-map-widgets)
* [Django-ssl-server](https://github.com/teddziuba/django-sslserver) (For testing)
* The following pip packages:
* google-api-python-client
* google-auth 
* google-auth-oauthlib 
* google-auth-httplib2
* tonyg-rfc3339
* django-pickefield
## How to run
On your first run and whenever a model is modified after your last run, run the following;
```
./manage.py makemigrations
./manage.py migrate
```
The following command runs the website on your computer:
```
./manage.py runsslserver localhost:8000
```
If in doubt, just run all 3.
Connect to `https://localhost:8000/` to see the result.
Due to constraints of Google API, only https works and only localhost:8000 works.

To add new events, call
```
./manage.py createsuperuser
```
and connect to `https://localhost:8000/admin`

## TIPS:
Ignore files not in event\_map directory.

Whenever an HTTP response is sent to server, the server runs a python function according to configuration in urls.py.

If you need to run some code without firing the server, use `./manage.py shell`

Each model represents relation between a Python object and a database table.
For example:
```
class User(models.Model):
name = CharField(...)
jason = User(name='Jason')
jason.save() # adds a user named Jason into the database
for user in User.objects.all():
print(user.name) # prints the name of every user
all_alice = User.objects.filter(name='Alice') # filter every user named alice
if len(all_alice) > 1: # If there are more than one Alice
...
else:
alice = all_alice.get() # You may call get() to get the object if you are sure there's only one object in the query.
```

## TODOS:
From post important to least important:
* Filter event by user available time. (DONE)
* Send emails. (DONE. use `./manage.py notify`)
* Write an event\_list template and view: https://docs.djangoproject.com/en/2.2/intro/tutorial03/ (DONE)
* Will have to add filter functionality. (DONE)
* Classify events. Use [choice](https://docs.djangoproject.com/en/2.2/ref/models/fields/#choices) (DONE)
* Let user save their preferences: e.g. which type of events they like, where does the user live (DONE)
* Write an event management page (It's least important 'cause there's a built-in one, but using that will be **very** obvious.) (DONE)

