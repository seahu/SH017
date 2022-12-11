-- radiatory_podkrovi

-- script ovlada vyhrivani podkrovi
-- v pripade, ze je akumulacka dostatecne nahrata a zaroven podkrovi studeny tak topi jinak netopi


-- ******************** MENITELNE PARAMETRY: ***************************************************************


local max_akumulacka=35
local min_akumulacka=30
local max_podkrovi=21.8
local min_podkrovi=21.6
local podkrovi_err=-40 -- hodnota znacici chybu teplotniho cidla (pod klesne pod tutu mez tak je to spatne namerene)

return {
	on = {
		devices = {
			'akumulacka_horni',
			'podkrovi'
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
            Switchs={ 'radiatory'},
            Temperatures={'akumulacka_horni', 'podkrovi'},
            SetPoints={},
            Variables={},
            Devices={}
        }

        log ('Start')
        dev, var = domoticz.helpers.ini(domoticz, used)
        if ( 
            dev['akumulacka_horni']>max_akumulacka and
            dev['podkrovi']<min_podkrovi and
            dev['podkrovi']>podkrovi_err
            ) then
                dev['radiatory']='On'
        elseif (
            dev['akumulacka_horni']<min_akumulacka or
            dev['podkrovi']>max_podkrovi
            ) then
                dev['radiatory']='Off'
        end
        domoticz.helpers.ending(domoticz, used, dev, var)
	end
}