from django.contrib import admin
from django.contrib.gis.db import models as gisModels
from mapwidgets.widgets import GooglePointFieldWidget
from . import models

# Register your models here.

@admin.register(models.Event, models.User)
class MapAdmin(admin.ModelAdmin):
    formfield_overrides = {
        gisModels.PointField: {'widget': GooglePointFieldWidget},
    }
