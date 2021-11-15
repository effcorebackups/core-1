<?php

  ##################################################################
  ### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          abstract class poll {

  static function select($id) {
    return (new instance('poll', ['id' => $id]))->select();
  }

  # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦

  static function answers_by_poll_id_select($id_poll) {
    $result = [];
    $rows = entity::get('poll_answer')->instances_select(['conditions' => [
      'id_poll_!f'       => 'id_poll',
      'id_poll_operator' => '=',
      'id_poll_!v'       => $id_poll]]);
    foreach ($rows as $c_row)
      $result[$c_row->id] = $c_row;
    return $result;
  }

  static function answer_insert($id_poll, $answer, $weight = 0) {
    return (new instance('poll_answer', [
      'id_poll' => $id_poll,
      'answer'  => $answer,
      'weight'  => $weight
    ]))->insert();
  }

  static function answer_delete($id_answer) {
    return (new instance('poll_answer', [
      'id' => $id_answer
    ]))->delete();
  }

  # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦

  static function votes_total_select($id_answers) {
    return entity::get('poll_vote')->instances_select_count(['conditions' => [
      'id_answer_!f'       => 'id_answer',
      'id_answer_in_begin' => 'in (',
      'id_answer_in_!a'    => $id_answers,
      'id_answer_in_end'   => ')'
    ]]);
  }

  # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦

  static function votes_id_total_by_answers_select($id_answers) {
    $result = [];
    $rows = entity::get('poll_vote')->instances_select([
      'fields'     => ['id_answer_!f' => 'id_answer', 'count' => ['function_begin' => 'count(', 'function_field' => '*', 'function_end' => ')', 'alias_begin' => 'as', 'alias' => 'total']],
      'conditions' => ['id_answer_!f' => 'id_answer', 'id_answer_in_begin' => 'in (', 'id_answer_in_!a' => $id_answers, 'id_answer_in_end' => ')'],
      'group'      => ['id_answer_!f' => 'id_answer']]);
    foreach ($rows as $c_row)
      $result[$c_row->id_answer] =
              $c_row->total;
    return $result;
  }

  static function votes_id_by_user_id_select($id_user, $id_answers) {
    $result = [];
    $rows = entity::get('poll_vote')->instances_select(['conditions' => [
      'id_user_!f'         => 'id_user',
      'id_user_operator'   => '=',
      'id_user_!v'         => $id_user,
      'conjunction'        => 'and',
      'id_answer_!f'       => 'id_answer',
      'id_answer_in_begin' => 'in (',
      'id_answer_in_!a'    => $id_answers,
      'id_answer_in_end'   => ')']]);
    foreach ($rows as $c_row)
      $result[$c_row->id_answer] =
              $c_row->id_answer;
    return $result;
  }

  static function votes_id_by_session_id_select($id_session, $id_answers) {
    $result = [];
    $rows = entity::get('poll_vote')->instances_select(['conditions' => [
      'id_session_!f'       => 'id_session',
      'id_session_operator' => '=',
      'id_session_!v'       => $id_session,
      'conjunction'         => 'and',
      'id_answer_!f'        => 'id_answer',
      'id_answer_in_begin'  => 'in (',
      'id_answer_in_!a'     => $id_answers,
      'id_answer_in_end'    => ')']]);
    foreach ($rows as $c_row)
      $result[$c_row->id_answer] =
              $c_row->id_answer;
    return $result;
  }

  # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦

  static function votes_by_user_id_insert($id_user, $id_answer) {
    return (new instance('poll_vote', [
      'id_user'   => $id_user,
      'id_answer' => $id_answer
    ]))->insert();
  }

  static function votes_by_session_id_insert($id_session, $id_answer) {
    return (new instance('poll_vote', [
      'id_session' => $id_session,
      'id_answer'  => $id_answer
    ]))->insert();
  }

  # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦

  static function votes_by_user_id_delete($id_user, $id_answers) {
    return entity::get('poll_vote')->instances_delete(['conditions' => [
      'id_user_!f'         => 'id_user',
      'id_user_operator'   => '=',
      'id_user_!v'         => $id_user,
      'conjunction'        => 'and',
      'id_answer_!f'       => 'id_answer',
      'id_answer_in_begin' => 'in (',
      'id_answer_in_!a'    => $id_answers,
      'id_answer_in_end'   => ')'
    ]]);
  }

  static function votes_by_session_id_delete($id_session, $id_answers) {
    return entity::get('poll_vote')->instances_delete(['conditions' => [
      'id_session_!f'       => 'id_session',
      'id_session_operator' => '=',
      'id_session_!v'       => $id_session,
      'conjunction'         => 'and',
      'id_answer_!f'        => 'id_answer',
      'id_answer_in_begin'  => 'in (',
      'id_answer_in_!a'     => $id_answers,
      'id_answer_in_end'    => ')'
    ]]);
  }

}}