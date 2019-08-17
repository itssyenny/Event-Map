from django import forms
from django.contrib.admin import widgets

from event_map import models
from mapwidgets.widgets import GooglePointFieldWidget, GoogleStaticMapWidget, GoogleStaticOverlayMapWidget

class DateTimeInputHTML5(forms.DateTimeInput):
    input_type = 'datetime-local'
    def __init__(self, *args, **kwargs):
        if 'format' not in kwargs: 
            kwargs['format'] = '%Y-%m-%dT%H:%M' 
        super().__init__(*args, **kwargs)

class DateTimeFieldHTML5(forms.DateTimeField):
    widget = DateTimeInputHTML5
    input_formats = ['%Y-%m-%dT%H:%M'] 

class EventForm(forms.ModelForm): 
    start = DateTimeFieldHTML5()
    end = DateTimeFieldHTML5()
    class Meta:
        model = models.Event
        fields = ("name", "event_type", "url", "description", "start", "end", "location")
        widgets = {
            'location': GooglePointFieldWidget,
            'start': DateTimeInputHTML5,
            'end': DateTimeInputHTML5,
        }

class FilterForm(forms.Form):
    start = DateTimeFieldHTML5()
    end = DateTimeFieldHTML5()

class UserForm(forms.ModelForm):
    ''' For user preference'''
    class Meta:
        model = models.User
        exclude = ('preference',)
        widgets = {
            'location': GooglePointFieldWidget,
        }
