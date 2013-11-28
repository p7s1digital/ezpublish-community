{include uri='design:myvideo_appendix/header.tpl' title='Info'}


<pre>
    ###### WARNING Nicht aktuell!!!! 2013-04-25 ######


DRAFT
=====

Im folgenden eine kurze Beschreibung der MyVideo ContentTypes.

Das eZ Publish CMS wird im folgenden kurz als "CMS" bezeichnet.
Das Symfony Frontend wird als "Frontend" bezeichnet.



MyVideo Sushibar
----------------
Die Sushibar dient lediglich als Container für ein Sushibar-Layout und eine Videoliste.
Eine Videoliste kann eine "MyVideo Videoliste" oder ein "MyVideo Chart" sein.

Der Titel und die Beschreibung zur Orientierung für die Redaktion.


MyVideo Slider
--------------
Der Slider ist im Prinzip ein Spezialfall der Sushibar.
Im Gegensatz zur Suhibar ist das Layout beim Slider fest vorgegeben.
Kann also nicht vom Redakteur ausgewählt werden.
Eine Videoliste wird genau wie bei der Sushibar ausgewählt.


MyVideo Sushibar-Layout
-----------------------
Das Sushibar-Layout legt fest wie eine Sushibar im Frontend dargestellt wird.
Entscheidend ist hier das Feld "Identifier" anhand dessen das Frontend die Sushibar zeichnet.
Alle weiteren Angaben (Titel, Beschreibung, Vorschaubild) dienen lediglich der Beschreibung und 
besseren Auffindbarkeit im CMS.

Wenn ein neues Sushibar-Layout erstellt werden soll muss das Frontend entsprechend für diesen
neuen Typ erweitert werden (Templates, CSS).
Im CMS kann das Sushibar-Layout direkt erstellt und eine neuer "Identifier" vorgegeben werden.


MyVideo SOLR Abfrage
--------------------
Hier wird eine SOLR-Query konfiguriert.
Aktuell müssen relevante Parameter als literal Strings eingepflegt werden.

Das wird vermutlich mittelfristig anders gelöst.



MyVideo Videoliste
------------------
Hier wird eine Videoliste aus mehreren (optionalen) Quellen komponiert.
Es wird unterschieden in drei Grundtypen:

a) redaktionelle Videoliste

b) dynamische Videoliste

c) Kombination aus redaktionell und dynamisch


zu a)

hier werden Video-Ids eingetragen.

zu b)

hier werden die VideoIds im Frontend von einer dynamischen Quelle (SOLR-Query) bezogen.


zu c)

hier wird eine dynamische Liste wie bei b) vorgegeben.
Einzelne Platzierungen können durch manuell vorgegebene Ids überschrieben werden.
Sozusagen ein "Sticky"-Feature.


MyVideo Chart
-------------
Dies ist ein Speziallfall der "normalen" MyVideo Videoliste.
Hier wird lediglich die Id des jeweiligen Chart gepflegt.


MyVideo Video
-------------
Hier wird die Id des zugehörigen Video und optionale Meta-Daten gepflegt.
Z.B. kann hier ein Titel vorgegeben werden.


MyVideo Menu
------------
Dient als Container und Wurzelelement für alle Menüs.
Bisher gibt es lediglich die Hauptnavigation.


MyVideo Menuitem
----------------
Ein einzelner Menüpunkt.

TODO, erläutern:
- Typ
- Mehrsprachigkeit
- Slug


MyVideo Flyout
--------------
Eine Grafik die einem Menüpunkt zugeordnet werden kann.


</pre>


{include uri='design:myvideo_appendix/footer.tpl'}


