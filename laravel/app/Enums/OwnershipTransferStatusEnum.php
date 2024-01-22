<?php

namespace App\Enums;

abstract class OwnershipTransferStatusEnum
{
    const WAITING_TRANSFER = 'WAITING_TRANSFER'; 
    const WAITING_DOCUMENT_TRANSFER = 'WAITING_DOCUMENT_TRANSFER'; 
    const WAITING_SEND_TRANSFER = 'WAITING_SEND_TRANSFER';
    const TRANSFERING = 'TRANSFERING';
    const TRANSFERED = 'TRANSFERED';
}

