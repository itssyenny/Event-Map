from django.urls import path
from django.conf.urls import url, include

from . import views

app_name = 'event_map'
urlpatterns = [
    path('oauth2_callback', views.oauth2_callback, name='oauth2_callback'),
    path('login', views.login, name='login'),
    path('logout', views.logout, name='logout'),
    path('event/create', views.EventCreateView.as_view(), name="create"),
    path('event/edit/<int:pk>', views.EventEditView.as_view(), name="edit"),
    path('event/edit/', views.EventListView.as_view(), name="edit_list"),
    path('preference', views.user_preference, name="user_preference"),
    path('', views.event_map, name='event_map'),
]
