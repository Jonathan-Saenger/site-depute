<?php

declare(strict_types=1);

namespace App\Enum;

enum CommuneEnum: string
{
  case FERRETTE = 'ferrette';
  case READERSDORF = 'readersdorf';
  case BENDORF = 'bendorf';
  case LIEGSDORF = 'liegsdorf';
  case BIEDERTHAL = 'biederthal';
  case BLOTZHEIM = 'blotzheim';
  case BRUEBACH = 'bruebach';
  case CHALAMPE = 'chalampe';
  case CHAVANNES = 'chavannes';
  case CHAVANNESSURLETANG = 'chavannes_sur_letang';
  case CHENEBOURG = 'chene_bourg';
  case CHENEBOURGERIE = 'chene_bourgeries';
  case CHENEPÂQUIER = 'chene_paquier';
  case CHENETHUY = 'chene_thuy';

  /**
   * Retourne le nom d'affichage de la commune
   */
  public function getLabel(): string
  {
      return match($this) {
          self::FERRETTE => 'Ferrette',
          self::READERSDORF => 'Readersdorf',
          self::BENDORF => 'Bendorf',
          self::LIEGSDORF => 'Liegsdorf',
          self::BIEDERTHAL => 'Biederthal',
          self::BLOTZHEIM => 'Blotzheim',
          self::BRUEBACH => 'Bruebach',
          self::CHALAMPE => 'Chalampé',
          self::CHAVANNES => 'Chavannes',
          self::CHAVANNESSURLETANG => 'Chavannes-sur-l\'Etang',
          self::CHENEBOURG => 'Chêne-Bourg',
          self::CHENEBOURGERIE => 'Chêne-Bourgeries',
          self::CHENEPÂQUIER => 'Chêne-Pâquier',
          self::CHENETHUY => 'Chêne-Thuy',
      };
  }
}
