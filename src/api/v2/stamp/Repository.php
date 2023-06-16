<?php

interface Repository
{
  public function select($company_id, $employee_id, $base_date);
}
