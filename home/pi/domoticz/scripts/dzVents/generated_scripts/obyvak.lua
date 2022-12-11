-- obyvak

-- script ovlada vyhrivani obyvak
-- v pripade, ze je akumulacka dostatecne nahrata a zaroven obyvak studeny tak topi jinak netopi


-- ******************** MENITELNE PARAMETRY: ***************************************************************


local max_akumulacka=35
local min_akumulacka=30
local max_obyvak=21.8
local min_obyvak=21.6

return {
	on = {
		devices = {
			'akumulacka_horni',
			'obyvak'
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
            Switchs={ 'rozdelovac_obyvak'},
            Temperatures={'akumulacka_horni', 'obyvak'},
            SetPoints={},
            Variables={},
            Devices={}
        }

        log ('Start')
        dev, var = domoticz.helpers.ini(domoticz, used)
        if ( 
            dev['akumulacka_horni']>max_akumulacka and
            dev['obyvak']<min_obyvak
            ) then
                dev['rozdelovac_obyvak']='On'
        elseif (
            dev['akumulacka_horni']<min_akumulacka or
            dev['obyvak']>max_obyvak
            ) then
                dev['rozdelovac_obyvak']='Off'
        end
        domoticz.helpers.ending(domoticz, used, dev, var)
	end
}