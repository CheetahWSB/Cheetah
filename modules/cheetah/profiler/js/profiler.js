/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

function ch_profiler_switch (e) {
    var a = e.parentNode.childNodes;
    var ee;
    for (var i in a) {
        if (a[i].className == 'ch_profiler_content') {
            ee = a[i];
            break;
        }
    }
    if (undefined == ee)
        return;
    if (ee.style.display == 'block') {
        e.innerHTML = '+';
        ee.style.display = 'none';
    } else {
        e.innerHTML = '-';
        ee.style.display = 'block';

        var t = ee.getElementsByTagName('td');
        for(var i=0,n=t.length;i<n;i++) {
            if (t[i].className.match(/^highlight$/)) {
                t[i].innerHTML = jush.highlight('sql', t[i].innerHTML).replace(/\t/g, '    ').replace(/(^|\n| ) /g, '$1 ');
                t[i].className = 'highlighted';
            }
        }
    }
}

