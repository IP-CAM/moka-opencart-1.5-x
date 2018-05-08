<?php

Class MokaConfig
{

	const max_installment = 12;

	public static function getAvailablePrograms()
	{
        return array(
            'axess' => array('name' => 'Axess', 'bank' => 'Akbank A.Ş.', 'installments' => true),
            'world' => array('name' => 'WordCard', 'bank' => 'Yapı Kredi Bankası', 'installments' => true),
            'bonus' => array('name' => 'BonusCard', 'bank' => 'Garanti Bankası A.Ş.', 'installments' => true),
            'cardfinans' => array('name' => 'CardFinans', 'bank' => 'FinansBank A.Ş.', 'installments' => true),
            'maximum' => array('name' => 'Maximum', 'bank' => 'T.C. İş Bankası', 'installments' => true),

        );
	}

    public static function setRatesFromPost($posted_data)
    {
        $banks = MokaConfig::getAvailablePrograms();
        $return = array();
        foreach ($banks as $k => $v) {
            $return[$k] = array();
            for ($i = 1; $i <= self::max_installment; $i++) {
                $return[$k]['installments'][$i]['value'] = isset($posted_data[$k]['installments'][$i]['value']) ? ((float) $posted_data[$k]['installments'][$i]['value']) : 0.0;
                $return[$k]['installments'][$i]['active'] = isset($posted_data[$k]['installments'][$i]['active']) ? ((int) $posted_data[$k]['installments'][$i]['active']) : 0;
            }
        }
        return $return;
    }

    public static function setRatesDefault()
    {
        $banks = MokaConfig::getAvailablePrograms();
        $return = array();
        foreach ($banks as $k => $v) {
            $return[$k] = array('active' => 0);
            for ($i = 1; $i <= self::max_installment; $i++) {
                $return[$k]['installments'][$i]['value'] = (float) (1 + $i + ($i / 5) + 0.1);
                $return[$k]['installments'][$i]['active'] = $v['installments'];
                if ($i == 1) {
                    $return[$k]['installments'][$i]['value'] = 0.00;
                    $return[$k]['installments'][$i]['active'] = 1;
                }
            }
        }
        return $return;
    }
	
	public static function setRatesNull()
    {
        $banks = MokaConfig::getAvailablePrograms();
        $return = array();
        foreach ($banks as $k => $v) {
            $return[$k] = array('active' => 0);
            for ($i = 1; $i <= self::max_installment; $i++) {
                $return[$k]['installments'][$i]['value'] = 0;
                $return[$k]['installments'][$i]['active'] = 0;
            }
        }
        return $return;
    }


	 public static function createRatesUpdateForm($rates)
    {
        $return = '<table class="moka_table table">'
                . '<thead>'
                . '<tr><th>Banka</th><th>Durum</th>';
        for ($i = 1; $i <= self::max_installment; $i++) {
            $return .= '<th>' . $i . ' taksit</th>';
        }
        $return .= '</tr></thead><tbody>';

        $banks = MokaConfig::getAvailablePrograms();
        foreach ($banks as $k => $v) {
            $return .= '<tr>'
					. '<th><img src="'.HTTPS_CATALOG.'catalog/view/theme/default/image/moka_payment/' . $k . '.svg" width="105px"></th>'
					. '<th><select  name="moka_rates[' . $k . '][active]" >'
						. '<option value="1">Aktif</option>'
						. '<option value="0" '.((int)$rates[$k]['active'] == 0 ? 'selected="selected"' : '').'>Pasif</option>'
                    .'</select></th>';
            for ($i = 1; $i <= self::max_installment; $i++) {
				if(!isset($rates[$k]['installments'][$i]['active']))
					$rates[$k]['installments'][$i]['active'] = 0;
				if(!isset($rates[$k]['installments'][$i]['value']))
					$rates[$k]['installments'][$i]['value'] = 0;
                $return .= '<td>'
                        . ' Aktif <input type="checkbox"  name="moka_rates[' . $k . '][installments][' . $i . '][active]" '
                        . ' value="1" ' . ((int) $rates[$k]['installments'][$i]['active'] == 1 ? 'checked="checked"' : '') . '/>'
                        . ' % <input type="number" step="0.01" maxlength="4" size="4" style="width:60px" '
                        . ((int) $rates[$k]['installments'][$i]['active'] == 0 ? 'disabled="disabled"' : '')
                        . ' value="' . ((float) $rates[$k]['installments'][$i]['value']) . '"'
                        . ' name="moka_rates[' . $k . '][installments][' . $i . '][value]"/></td>';
            }
            $return .= '</tr>';
        }
        $return .= '</tbody></table>';
        return $return;
    }

    public static function calculatePrices($price, $rates)
    {
        $banks = MokaConfig::getAvailablePrograms();
        $return = array();
        foreach ($banks as $k => $v) {
            if($v['installments'] == false)
                continue;
          $return[$k] = array('active' => $rates[$k]['active']);
            for ($i = 1; $i <= self::max_installment; $i++) {
                $return[$k]['installments'][$i] = array(
					'active' =>$rates[$k]['installments'][$i]['active'],
                    'total' => number_format((((100 + $rates[$k]['installments'][$i]['value']) * $price) / 100), 2, '.', ''),
                    'monthly' => number_format((((100 + $rates[$k]['installments'][$i]['value']) * $price) / 100) / $i, 2, '.', ''),
                );
            }
        }
        return $return;
    }
	

}
