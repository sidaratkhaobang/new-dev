<?php

namespace UndObs\Internal\Signature;

use UndObs\Internal\Common\Model;

interface SignatureInterface
{
	function doAuth(array &$requestConfig, array &$params, Model $model);
}