import google.oauth2.credentials
import pickle
from google_auth_oauthlib.flow import Flow
from googleapiclient import discovery
from django.urls import reverse
from django.http import HttpResponseRedirect, HttpResponseBadRequest

CLIENT_SECRETE_FILE = 'client_secret.json'
SCOPES = ['https://www.googleapis.com/auth/calendar.readonly', 'https://www.googleapis.com/auth/userinfo.email' , 'openid']

### Helper functions

def get_flow(*args, **kwargs):
    return Flow.from_client_secrets_file(
            CLIENT_SECRETE_FILE,
            scopes = SCOPES, *args, **kwargs)

def get_email(credentials):
    oauth2 = discovery.build('oauth2', 'v2', credentials = credentials)
    request = oauth2.userinfo().get(fields='email')
    response = request.execute()
    return response['email']
