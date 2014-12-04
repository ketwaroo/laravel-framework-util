ketwaroo/laravel-framework-util Content Filter
===============================


Borrowed from wordpress where there was the notion of hooks and filters.

Hooks allow running custom code at certain entry points in the main script.

Filters allow operations to be queued on actual content where the final result of
the operations is returned replaces the content in question.

Laravel already has something similar to hooks in the form of Events.

This implementation of content filters makes use of the laravel event system to 
queue callables onto a payload.
