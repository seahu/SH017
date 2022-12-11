-- krbove_cerpadlo

-- script ovlada krbove cerpadlo, ktere se zapina kdyz je krb teplejsi nez akumulacka nebo je teplejsi nez prednastavena min. teplota krbu (min_krb)
-- a vypina kdyz je teplota krbu mensi nez aku a zaroven meni nez prednastavena min. teplota krbu 

-- ******************** MENITELNE PARAMETRY: ***************************************************************


local min_krb=75

return {
	on = {
		devices = {
			'akumulacka_dolni',
			'krb'
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
            Switchs={ 'krb_sw'},
            Temperatures={'akumulacka_dolni', 'krb'},
            SetPoints={},
            Variables={},
            Devices={}
        }

        log ('Start')
        dev, var = domoticz.helpers.ini(domoticz, used)
        if ( 
            dev['krb']>dev['akumulacka_dolni'] or
            dev['krb']>min_krb
            ) then
                dev['krb_sw']='On'
        elseif (
            dev['krb']<dev['akumulacka_dolni'] and
            dev['krb']<min_krb
            ) then
                 dev['krb_sw']='Off'
        end
        domoticz.helpers.ending(domoticz, used, dev, var)
	end
}