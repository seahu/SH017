-- podlahovka_cerpadlo

-- script ovlada cerpadlo podlahovky kdyz je oteven nektery z rozdelovacu spusti cerpadlo jinak ho vypne


-- ******************** MENITELNE PARAMETRY: ***************************************************************


local max_akumulacka=35
local min_akumulacka=30
local max_obyvak=21.8
local min_obyvak=21.6

return {
	on = {
		devices = {
			'rozdelovac_pokoj_chodba_zachod',
			'rozdelova_predsin',
			'rozdelovac_obyvak',
			'rozdelovac_koupelna'
		}
	},
	logging = {
		level = domoticz.LOG_INFO, -- mozne urovne logovani jsou domoticz.LOG_INFO, domoticz.LOG_MODULE_EXEC_INFO, domoticz.LOG_DEBUG or domoticz.LOG_ERROR . domoticz.LOG_ERROR vypne logovani pro tento script
		marker = 'bojler',
	},
	execute = function(domoticz, triggeredItem)
		domoticz.log('Device ' .. triggeredItem.name .. ' was changed', domoticz.LOG_INFO)
        --log=domoticz.log
        log = function(text, offset, level)
            domoticz.helpers.log(domoticz, text, offset, level)
        end

	    used={
            Switchs={   'rozdelovac_pokoj_chodba_zachod',
			            'rozdelova_predsin',
			            'rozdelovac_obyvak',
			            'rozdelovac_koupelna',
			            'podlahovka_sw'},
            Temperatures={},
            SetPoints={},
            Variables={},
            Devices={}
        }

        log ('Start')
        dev, var = domoticz.helpers.ini(domoticz, used)
        if ( 
            dev['rozdelovac_pokoj_chodba_zachod']=='On' or
            dev['rozdelova_predsin']=='On' or
            dev['rozdelovac_obyvak']=='On' or
            dev['rozdelovac_koupelna']=='On'
            ) then
                dev['podlahovka_sw']='On'
        else 
                dev['podlahovka_sw']='Off'
        end
        domoticz.helpers.ending(domoticz, used, dev, var)
	end
}