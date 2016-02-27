<?php


#	Errors
class H2o_Error extends Exception {}
// PHP now has an exception called ParseError
// but h2o doesn't seem like it uses it so I am commenting it out
// for a future removal
//class ParseError extends H2o_Error {}
class TemplateNotFound extends H2o_Error {}
class TemplateSyntaxError extends H2o_Error {}

?>