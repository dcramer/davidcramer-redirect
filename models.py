from google.appengine.ext import db

class Redirect(db.Model):
    origin = db.StringProperty()
    dest = db.StringProperty()