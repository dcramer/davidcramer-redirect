import datetime
from google.appengine.ext import db
from google.appengine.tools import bulkloader
import models

class RedirectLoader(bulkloader.Loader):
    def __init__(self):
        bulkloader.Loader.__init__(self, 'Redirect',
                                   [('origin', str),
                                    ('dest', str),
                                   ])

loaders = [RedirectLoader]