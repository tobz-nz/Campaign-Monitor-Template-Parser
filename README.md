This is a really basic parser for Campign Monitor template tags. This is a work in progress and is buggy.
The main bugs with it are that your tag attributes must be in a particular order (some regex guru can probably fix this pretty easily)

USAGE:
Simply name your template "template.html" and have it in the same folder as cmparse.php, then run cmparse.php in your browser.
Or run cmparse.php?template=relative_path_to_your_template_file.html

Not all the tags are currently supported, but here is a list of what is:

<$title default='asdsd' link='true'$>
<$description default='asdsd'$>
<$imagesrc default='images/default.png' link='true'$>
<$repeater$>, <$repeatertitle$>, <$tableofcontents$> (these tags are just stripped from the output)

All the date variables are supported
<$currentday$>
<$currentdayname$>
<$currentmonth$>
<$currentmonthname$>
<$currentyear$>

All the special links are supported
<unsubscribe></unsubscribe>
<webversion></webversion>
<forwardtoafriend></forwardtoafriend>
<preferences></preferences>
<fblike></fblike>
<tweet></tweet>

Subscriber variables are supported , including fallbacks
[firstname, fallback=]
[lastname, fallback=]
[fullname, fallback=]
[email, fallback=]