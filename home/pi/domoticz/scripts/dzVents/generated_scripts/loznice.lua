-- loznice

-- script ovlada vyhrivani loznice
-- v pripade, ze je akumulacka dostatecne nahrata a zaroven loznice studena tak topi jinak netopi
-- proc ovlada rozdelovace zdanlive smerujici jinam nez do loznice neresim

-- ******************** MENITELNE PARAMETRY: ***************************************************************


local max_akumulacka=35
local min_akumulacka=30
local max_loznice=20.6
local min_loznice=20.3

return {
	on = {
		devices = {
			'akumulacka_horni',
			'loznice'
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
            Switchs={ 'rozdelovac_pokoj_chodba_zachod','rozdelova_predsin'},
            Temperatures={'akumulacka_horni', 'loznice'},
            SetPoints={},
            Variables={},
            Devices={}
        }

        log ('Start')
        dev, var = domoticz.helpers.ini(domoticz, used)
        if ( 
            dev['akumulacka_horni']>max_akumulacka and
            dev['loznice']<min_loznice
            ) then
                dev['rozdelovac_pokoj_chodba_zachod']='On'
                dev['rozdelova_predsin']='On'
        elseif (
            dev['akumulacka_horni']<min_akumulacka or
            dev['loznice']>max_loznice
            ) then
                dev['rozdelovac_pokoj_chodba_zachod']='Off'
                dev['rozdelova_predsin']='Off'
        end
        domoticz.helpers.ending(domoticz, used, dev, var)
	end
}