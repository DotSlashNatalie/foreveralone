<?php

namespace system\engine;

//Original idea by http://www.php.net/manual/en/function.set-error-handler.php#112881

class WarningException              extends \ErrorException {}
class ParseException                extends \ErrorException {}
class NoticeException               extends \ErrorException {}
class CoreErrorException            extends \ErrorException {}
class CoreWarningException          extends \ErrorException {}
class CompileErrorException         extends \ErrorException {}
class CompileWarningException       extends \ErrorException {}
class UserErrorException            extends \ErrorException {}
class UserWarningException          extends \ErrorException {}
class UserNoticeException           extends \ErrorException {}
class StrictException               extends \ErrorException {}
class RecoverableErrorException     extends \ErrorException {}
class DeprecatedException           extends \ErrorException {}
class UserDeprecatedException       extends \ErrorException {}