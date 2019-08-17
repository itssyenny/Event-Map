from datetime import datetime, timedelta, timezone
import rfc3339
import pickle

from django.contrib.gis.db import models
from django.contrib.gis.geos import Point
from django.contrib.gis.measure import Distance
from django.db.models import Q # query object
from django.utils import timezone
from django.urls import reverse
from picklefield.fields import PickledObjectField 

from .calendar import get_busy_intervals
from .oauth2 import get_email
from .constants import event_types


class TimeInterval(models.Model):
    start = models.DateTimeField()
    end = models.DateTimeField()
    class Meta:
        abstract = True

class Event(TimeInterval):
    name = models.CharField(max_length = 255)
    url = models.URLField(blank = True, max_length = 255)
    description = models.TextField(blank = True) # Allow empty description
    location = models.PointField(help_text="Location of this event.")
    event_type = models.CharField(choices = zip(event_types, event_types), max_length = 50)

    @classmethod
    def within_interval(cls, start, end):
        ''' Given a 2-tuple of datetime, query the events within this tuple. '''
        return cls.objects.filter(start__gt=start, end__lt=end)
    
    def get_absolute_url(self):
        return reverse('event_map:edit_list') # TODO: rederect to self... fix when the form is fixed

class User(models.Model):
    google_email = models.EmailField(primary_key = True, editable=False) # Identifier of each user
    credentials = PickledObjectField(editable = False)
    email = models.EmailField()
    notify_before_days = models.IntegerField(default = 1)
    preference = models.TextField(default='', blank = True)
    
    location = models.PointField(
            help_text = "Restrict the event you receive around this location", 
            null = True, blank = True) # allow to be empty
    distance = models.FloatField(
            help_text = "Restrict the events you receive around the location by this distance, in kilometers.",
            null = True, blank = True) # allow to be empty

    def save(self, *args, **kwargs):
        if not getattr(self, 'google_email', None):
            self.google_email = get_email(self.credentials)
        if not getattr(self, 'email', None):
            self.email = self.google_email
        super().save(*args, **kwargs) 

    def _get_busy_intervals(self, start, end):
        '''
        Gets time intervals in which the user is busy.
        '''
        return get_busy_intervals(self.credentials, start, end) 

    def _time_filter(self, start, end):
        ret = Q()
        for interval in self._get_busy_intervals(start, end):
            overlapping = Q(start__lt=interval.end) & Q(end__gt = interval.start)
            ret &= ~overlapping #exclude overlapping events
        return ret

    def _loc_filter(self):
        if self.location and self.distance:
            return Q(location__distance_lte=(self.location, Distance(km=self.distance)))
        else:
            return Q()


    def _preference_filter(self):
        if self.preference:
            ret = Q()
            preferred_types = self.preference.split(',')
            for event_type in preferred_types:
                ret |= Q(event_type = event_type)
            return ret
        return Q()


    def event_filter(self, start, end):
        '''
        Return an object that can be used to query the events the user preferes.
        '''
        return self._preference_filter() & self._time_filter(start, end) & self._loc_filter()
