from __future__ import print_function
import datetime
import pickle
import os.path
import rfc3339
from googleapiclient.discovery import build
from google_auth_oauthlib.flow import InstalledAppFlow
from google.auth.transport.requests import Request

# If modifying these scopes, delete the file token.pickle.
SCOPES = ['https://www.googleapis.com/auth/calendar.readonly']
def str_to_date(date_str):
    return datetime.datetime.strptime(date_str,'%Y-%m-%dT%H:%M:%SZ')

def date_to_rfc(d):
    return rfc3339.rfc3339(d)
def main():
    """Shows basic usage of the Google Calendar API.
    Prints the start and name of the next 10 events on the user's calendar.
    """
    creds = None
    # The file token.pickle stores the user's access and refresh tokens, and is
    # created automatically when the authorization flow completes for the first
    # time.
    if os.path.exists('token.pickle'):
        with open('token.pickle', 'rb') as token:
            creds = pickle.load(token)
    # If there are no (valid) credentials available, let the user log in.
    if not creds or not creds.valid:
        if creds and creds.expired and creds.refresh_token:
            creds.refresh(Request())
        else:
            flow = InstalledAppFlow.from_client_secrets_file(
                'credentials.json', SCOPES)
            creds = flow.run_local_server()
        # Save the credentials for the next run
        with open('token.pickle', 'wb') as token:
            pickle.dump(creds, token)

    service = build('calendar', 'v3', credentials=creds)

    # Call the Calendar API
    now = datetime.datetime.utcnow().isoformat() + 'Z' # 'Z' indicates UTC time
    print('Getting the upcoming 10 events')
    events_result = service.events().list(calendarId='primary', timeMin=now,
                                        maxResults=10, singleEvents=True,
                                             orderBy='startTime').execute()
    
    events = events_result.get('items', [])

    if not events:
        print('No upcoming events found.')
    for event in events:
        start = event['start'].get('dateTime', event['start'].get('date'))
        print(start, event['summary'])
    ending = (datetime.datetime.utcnow()+datetime.timedelta(7)).isoformat()+'Z'
    page_token = None
    i = 0
    true_id = []
    while True:
        calendar_list = service.calendarList().list(pageToken=page_token).execute()
        for calendar_list_entry in calendar_list['items']:
            print (calendar_list_entry['id'])
            true_id.append(calendar_list_entry['id'])
        page_token = calendar_list.get('nextPageToken')
        if not page_token:
            break
    
    print (true_id)
    foo = { "items":[{ "id" : true_id[0] }] ,"timeMin":now, "timeMax":ending}
    freebusy = service.freebusy().query(body=foo).execute()
    print(freebusy)
    calendar_dict = freebusy['calendars']
    busy_list = next(iter(calendar_dict.values()))['busy']
    print()
    result_list = [
        (str_to_date(item['start']), str_to_date(item['end'])) for item in busy_list
    ]
    rfc_result_list = list(map(lambda x:(date_to_rfc(x[0]), date_to_rfc(x[1])), result_list))
    #breakpoint()
    for i, item in enumerate(busy_list):
        temp = []
        temp0 = []
        temp.append(str_to_date(item['start']))
        temp0.append(date_to_rfc(str_to_date(item['start'])))
        temp.append(str_to_date(item['end']))
        temp0.append(date_to_rfc( str_to_date( item['end'] ) ))
        result_list.append(temp)
        rfc_result_list.append(temp0)
    
    print (rfc_result_list[0][0])

if __name__ == '__main__':
    main()
