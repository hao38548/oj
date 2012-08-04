<?php
class ReportController extends ApplicationController
{
  public function index()
  {
    $conditions = array();
    if (!User::can('view-any-report')) {
      $conditions['visible='] = TRUE;
    }
    $this->reports = fRecordSet::build('Report', $conditions, array('id' => 'desc'));
    $this->nav_class = 'reports';
    $this->render('report/index');
  }
  
  public function show($id)
  {
    try {
      $this->report = new Report($id);
      if (!$this->report->isReadable()) {
        throw new fAuthorizationException('You are not allowed to view this report.');
      }
      
      $p  = $this->report->getProblems();
      $un = $this->report->getUsernames();
      $up = $this->report->getUserPairs();
      
      $un[] = '';
      $up[] = array('id' => '', 'name' => 'Average');
      
      $st = $this->report->getStartDatetime();
      $et = $this->report->getEndDatetime();
      
      $this->board = new BoardTable(ReportGenerator::headers($p), $up, ReportGenerator::scores($p, $un, $st, $et));
      
      $this->nav_class = 'reports';
      $this->render('report/show');
    } catch (fExpectedException $e) {
      fMessaging::create('warning', $e->getMessage());
      fURL::redirect(Util::getReferer());
    } catch (fUnexpectedException $e) {
      fMessaging::create('error', $e->getMessage());
      fURL::redirect(Util::getReferer());
    }
  }
}
