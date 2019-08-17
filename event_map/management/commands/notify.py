from datetime import timedelta

from django.core.management import BaseCommand
from django.core.mail import send_mail
from event_map.models import User, Event
from django.urls import reverse 
from django.conf import settings
from django.utils.html import strip_tags
from django.utils.timezone import now
from django.template.loader import render_to_string

class Command(BaseCommand):
    help = 'Send event notification to each user'
    def handle(self, *args, **kwargs):
        sender = settings.EMAIL_HOST_USER
        subject = 'Events for you to attend'

        for user in User.objects.all():
            start = now() + timedelta(user.notify_before_days)
            end = start + timedelta(1)
            busy_filter = user.event_filter(start, end)
            events = Event.within_interval(start,end).filter(busy_filter) 
            if events:
                html_msg = render_to_string('event_map/mail_template.html', {'event_list':events})
                plain_msg = strip_tags(html_msg)
                send_mail(subject, plain_msg , sender, [user.email], fail_silently=False, html_message = html_msg) 
