<?php

namespace App\Enums;

abstract class LitigationCaseStatusEnum
{
    const PREPARE = 'PREPARE'; //เตรียมข้อมูลยื่นฟ้อง
    const MEDIATION = 'MEDIATION';  // นัดไกล่เกลี่ยฯ / ไต่สวนมูลฟ้อง
    const INVESTIGATE = 'INVESTIGATE'; // นัดสืบพยาน
    const VERDICT_HEARING = 'VERDICT_HEARING'; // นัดฟังคำพิพากษา
    const BETWEEN_APPEALING = 'BETWEEN_APPEALING'; // ระหว่างอุทธรณ์
    const BETWEEN_SUPREME_COURT = 'BETWEEN_SUPREME_COURT'; // ระหว่างฎีกา
    const WEALTH_INVESTIGATE = 'WEALTH_INVESTIGATE'; // ระหว่างสืบทรัพย์ / บังคับคดี
    const CASE_FINAL = 'CASE_FINAL'; // คดีสิ้นสุด

    const DAILY_RECORD = 'DAILY_RECORD'; // แจ้งความลงบันทึกประจำวัน
    const REPORT = 'REPORT'; // แจ้งความเพื่อดำเนินคดี
    const SUMMON = 'SUMMON'; // ออกหมายเรียก
    const ARREST_WARRANT = 'ARREST_WARRANT'; // ออกหมายจับ
    const SUE = 'SUE'; // ฟ้องศาล
}