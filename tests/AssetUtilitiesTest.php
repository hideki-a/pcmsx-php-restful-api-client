<?php

require_once 'vendor/autoload.php';
require_once 'src/classes/AssetUtilities.php';

use PHPUnit\Framework\TestCase;
use PowerCMSX\RESTfulAPI\AssetUtilities;

class AssetUtilitiesTest extends TestCase
{
    protected function setUp(): void
    {
        $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR);
        $dotenv->load();
    }

    public function test_画像の変換(): void
    {
        $result = AssetUtilities::encodeBase64(__DIR__ . '/assets/test.png');
        $expect = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAMAAAADCAMAAABh9kWNAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyhpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDkuMS1jMDAyIDc5LmRiYTNkYTNiNSwgMjAyMy8xMi8xNS0xMDo0MjozNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIDI1LjYgKE1hY2ludG9zaCkiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6MDE4OTI1ODNFNDNDMTFFRTg4RjFFQkNCNDIzMTY1MkMiIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6MDE4OTI1ODRFNDNDMTFFRTg4RjFFQkNCNDIzMTY1MkMiPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0ieG1wLmlpZDowMTg5MjU4MUU0M0MxMUVFODhGMUVCQ0I0MjMxNjUyQyIgc3RSZWY6ZG9jdW1lbnRJRD0ieG1wLmRpZDowMTg5MjU4MkU0M0MxMUVFODhGMUVCQ0I0MjMxNjUyQyIvPiA8L3JkZjpEZXNjcmlwdGlvbj4gPC9yZGY6UkRGPiA8L3g6eG1wbWV0YT4gPD94cGFja2V0IGVuZD0iciI/PvPD5YMAAAAGUExURRkZGf///8HhIWgAAAARSURBVHjaYmBgZAAiMAEQYAAAJAAFWfKsdgAAAABJRU5ErkJggg==';
        $this->assertSame($expect, $result);
    }

    public function test_ファイルの変換(): void
    {
        if ($_ENV['PROTOTYPE_PATH']) {
            require_once $_ENV['PROTOTYPE_PATH'] . '/lib/Prototype/class.PTUtil.php';
        } else {
            require_once 'powercmsx/PTUtil.php';
        }
        $result = AssetUtilities::encodeBase64(__DIR__ . '/assets/test.pdf');
        $expect = 'data:application/pdf;base64,JVBERi0xLjMKJcTl8uXrp/Og0MTGCjMgMCBvYmoKPDwgL0ZpbHRlciAvRmxhdGVEZWNvZGUgL0xlbmd0aCAxNjUgPj4Kc3RyZWFtCngBjc89C4MwEAbg3V/x9sM2GYx3JgZdW7p0E26rToEOBQfx/0OjGUpLB8lwueO4525ChwnldWaEGbS+OcQSmcqlfPm0xjt4y6auEUZcBAUZZmshAS41xmDZGyJycNRkMqIUqcCQJx5Qu71GwcxQB40BcsdNVn0DRcRx4BfVVMZSu0j4lY756dyryEWr1xuxLN31OcY1bMj7P4TKNeSV1u/e9sxCQAplbmRzdHJlYW0KZW5kb2JqCjEgMCBvYmoKPDwgL1R5cGUgL1BhZ2UgL1BhcmVudCAyIDAgUiAvUmVzb3VyY2VzIDQgMCBSIC9Db250ZW50cyAzIDAgUiAvTWVkaWFCb3ggWzAgMCA1OTUuMjggODQxLjg5XQo+PgplbmRvYmoKNCAwIG9iago8PCAvUHJvY1NldCBbIC9QREYgL1RleHQgXSAvQ29sb3JTcGFjZSA8PCAvQ3MxIDUgMCBSID4+IC9Gb250IDw8IC9UVDIgNyAwIFIKPj4gPj4KZW5kb2JqCjggMCBvYmoKPDwgL04gMyAvQWx0ZXJuYXRlIC9EZXZpY2VSR0IgL0xlbmd0aCAyNjEyIC9GaWx0ZXIgL0ZsYXRlRGVjb2RlID4+CnN0cmVhbQp4AZ2Wd1RT2RaHz703vdASIiAl9Bp6CSDSO0gVBFGJSYBQAoaEJnZEBUYUESlWZFTAAUeHImNFFAuDgmLXCfIQUMbBUURF5d2MawnvrTXz3pr9x1nf2ee319ln733XugBQ/IIEwnRYAYA0oVgU7uvBXBITy8T3AhgQAQ5YAcDhZmYER/hEAtT8vT2ZmahIxrP27i6AZLvbLL9QJnPW/3+RIjdDJAYACkXVNjx+JhflApRTs8UZMv8EyvSVKTKGMTIWoQmirCLjxK9s9qfmK7vJmJcm5KEaWc4ZvDSejLtQ3pol4aOMBKFcmCXgZ6N8B2W9VEmaAOX3KNPT+JxMADAUmV/M5yahbIkyRRQZ7onyAgAIlMQ5vHIOi/k5aJ4AeKZn5IoEiUliphHXmGnl6Mhm+vGzU/liMSuUw03hiHhMz/S0DI4wF4Cvb5ZFASVZbZloke2tHO3tWdbmaPm/2d8eflP9Pch6+1XxJuzPnkGMnlnfbOysL70WAPYkWpsds76VVQC0bQZA5eGsT+8gAPIFALTenPMehmxeksTiDCcLi+zsbHMBn2suK+g3+5+Cb8q/hjn3mcvu+1Y7phc/gSNJFTNlReWmp6ZLRMzMDA6Xz2T99xD/48A5ac3Jwyycn8AX8YXoVVHolAmEiWi7hTyBWJAuZAqEf9Xhfxg2JwcZfp1rFGh1XwB9hTlQuEkHyG89AEMjAyRuP3oCfetbEDEKyL68aK2Rr3OPMnr+5/ofC1yKbuFMQSJT5vYMj2RyJaIsGaPfhGzBAhKQB3SgCjSBLjACLGANHIAzcAPeIACEgEgQA5YDLkgCaUAEskE+2AAKQTHYAXaDanAA1IF60AROgjZwBlwEV8ANcAsMgEdACobBSzAB3oFpCILwEBWiQaqQFqQPmULWEBtaCHlDQVA4FAPFQ4mQEJJA+dAmqBgqg6qhQ1A99CN0GroIXYP6oAfQIDQG/QF9hBGYAtNhDdgAtoDZsDscCEfCy+BEeBWcBxfA2+FKuBY+DrfCF+Eb8AAshV/CkwhAyAgD0UZYCBvxREKQWCQBESFrkSKkAqlFmpAOpBu5jUiRceQDBoehYZgYFsYZ44dZjOFiVmHWYkow1ZhjmFZMF+Y2ZhAzgfmCpWLVsaZYJ6w/dgk2EZuNLcRWYI9gW7CXsQPYYew7HA7HwBniHHB+uBhcMm41rgS3D9eMu4Drww3hJvF4vCreFO+CD8Fz8GJ8Ib4Kfxx/Ht+PH8a/J5AJWgRrgg8hliAkbCRUEBoI5wj9hBHCNFGBqE90IoYQecRcYimxjthBvEkcJk6TFEmGJBdSJCmZtIFUSWoiXSY9Jr0hk8k6ZEdyGFlAXk+uJJ8gXyUPkj9QlCgmFE9KHEVC2U45SrlAeUB5Q6VSDahu1FiqmLqdWk+9RH1KfS9HkzOX85fjya2Tq5FrleuXeyVPlNeXd5dfLp8nXyF/Sv6m/LgCUcFAwVOBo7BWoUbhtMI9hUlFmqKVYohimmKJYoPiNcVRJbySgZK3Ek+pQOmw0iWlIRpC06V50ri0TbQ62mXaMB1HN6T705PpxfQf6L30CWUlZVvlKOUc5Rrls8pSBsIwYPgzUhmljJOMu4yP8zTmuc/jz9s2r2le/7wplfkqbip8lSKVZpUBlY+qTFVv1RTVnaptqk/UMGomamFq2Wr71S6rjc+nz3eez51fNP/k/IfqsLqJerj6avXD6j3qkxqaGr4aGRpVGpc0xjUZmm6ayZrlmuc0x7RoWgu1BFrlWue1XjCVme7MVGYls4s5oa2u7act0T6k3as9rWOos1hno06zzhNdki5bN0G3XLdTd0JPSy9YL1+vUe+hPlGfrZ+kv0e/W3/KwNAg2mCLQZvBqKGKob9hnmGj4WMjqpGr0SqjWqM7xjhjtnGK8T7jWyawiZ1JkkmNyU1T2NTeVGC6z7TPDGvmaCY0qzW7x6Kw3FlZrEbWoDnDPMh8o3mb+SsLPYtYi50W3RZfLO0sUy3rLB9ZKVkFWG206rD6w9rEmmtdY33HhmrjY7POpt3mta2pLd92v+19O5pdsN0Wu067z/YO9iL7JvsxBz2HeIe9DvfYdHYou4R91RHr6OG4zvGM4wcneyex00mn351ZzinODc6jCwwX8BfULRhy0XHhuBxykS5kLoxfeHCh1FXbleNa6/rMTdeN53bEbcTd2D3Z/bj7Kw9LD5FHi8eUp5PnGs8LXoiXr1eRV6+3kvdi72rvpz46Pok+jT4Tvna+q30v+GH9Av12+t3z1/Dn+tf7TwQ4BKwJ6AqkBEYEVgc+CzIJEgV1BMPBAcG7gh8v0l8kXNQWAkL8Q3aFPAk1DF0V+nMYLiw0rCbsebhVeH54dwQtYkVEQ8S7SI/I0shHi40WSxZ3RslHxUXVR01Fe0WXRUuXWCxZs+RGjFqMIKY9Fh8bFXskdnKp99LdS4fj7OIK4+4uM1yWs+zacrXlqcvPrpBfwVlxKh4bHx3fEP+JE8Kp5Uyu9F+5d+UE15O7h/uS58Yr543xXfhl/JEEl4SyhNFEl8RdiWNJrkkVSeMCT0G14HWyX/KB5KmUkJSjKTOp0anNaYS0+LTTQiVhirArXTM9J70vwzSjMEO6ymnV7lUTokDRkUwoc1lmu5iO/kz1SIwkmyWDWQuzarLeZ0dln8pRzBHm9OSa5G7LHcnzyft+NWY1d3Vnvnb+hvzBNe5rDq2F1q5c27lOd13BuuH1vuuPbSBtSNnwy0bLjWUb326K3tRRoFGwvmBos+/mxkK5QlHhvS3OWw5sxWwVbO3dZrOtatuXIl7R9WLL4oriTyXckuvfWX1X+d3M9oTtvaX2pft34HYId9zd6brzWJliWV7Z0K7gXa3lzPKi8re7V+y+VmFbcWAPaY9kj7QyqLK9Sq9qR9Wn6qTqgRqPmua96nu37Z3ax9vXv99tf9MBjQPFBz4eFBy8f8j3UGutQW3FYdzhrMPP66Lqur9nf19/RO1I8ZHPR4VHpcfCj3XVO9TXN6g3lDbCjZLGseNxx2/94PVDexOr6VAzo7n4BDghOfHix/gf754MPNl5in2q6Sf9n/a20FqKWqHW3NaJtqQ2aXtMe9/pgNOdHc4dLT+b/3z0jPaZmrPKZ0vPkc4VnJs5n3d+8kLGhfGLiReHOld0Prq05NKdrrCu3suBl69e8blyqdu9+/xVl6tnrjldO32dfb3thv2N1h67npZf7H5p6bXvbb3pcLP9luOtjr4Ffef6Xfsv3va6feWO/50bA4sG+u4uvnv/Xtw96X3e/dEHqQ9eP8x6OP1o/WPs46InCk8qnqo/rf3V+Ndmqb307KDXYM+ziGePhrhDL/+V+a9PwwXPqc8rRrRG6ketR8+M+YzderH0xfDLjJfT44W/Kf6295XRq59+d/u9Z2LJxPBr0euZP0reqL45+tb2bedk6OTTd2nvpqeK3qu+P/aB/aH7Y/THkensT/hPlZ+NP3d8CfzyeCZtZubf94Tz+wplbmRzdHJlYW0KZW5kb2JqCjUgMCBvYmoKWyAvSUNDQmFzZWQgOCAwIFIgXQplbmRvYmoKMiAwIG9iago8PCAvVHlwZSAvUGFnZXMgL01lZGlhQm94IFswIDAgNTk1LjI4IDg0MS44OV0gL0NvdW50IDEgL0tpZHMgWyAxIDAgUiBdID4+CmVuZG9iago5IDAgb2JqCjw8IC9UeXBlIC9DYXRhbG9nIC9QYWdlcyAyIDAgUiA+PgplbmRvYmoKNyAwIG9iago8PCAvVHlwZSAvRm9udCAvU3VidHlwZSAvVHJ1ZVR5cGUgL0Jhc2VGb250IC9BQUFBQUMrWXVNaW5jaG8tUmVndWxhciAvRm9udERlc2NyaXB0b3IKMTAgMCBSIC9Ub1VuaWNvZGUgMTEgMCBSIC9GaXJzdENoYXIgMzMgL0xhc3RDaGFyIDQxIC9XaWR0aHMgWyA3MjYgNDkwIDQwOQozNDYgMjYwIDMzMyAyODcgMjY4IDI3MyBdID4+CmVuZG9iagoxMSAwIG9iago8PCAvTGVuZ3RoIDI3NyAvRmlsdGVyIC9GbGF0ZURlY29kZSA+PgpzdHJlYW0KeAFdkc9qwzAMxu9+Ch27Q4mTNmkLIVA6CjnsD8v2AImtBMPiGMc55O0nuV0HO3yHn6TPfJKTS/1cWxMgefeTajBAb6z2OE+LVwgdDsaKNANtVLhTrKmxdSIhc7POAcfa9hOUpQBIPsgyB7/C5qynDp+49uY1emMH2HxdmlhpFue+cUQbQIqqAo09PffSutd2REiidVtr6puwbsn1N/G5OgRKRI70FklNGmfXKvStHVCUUlbl9VoJtPpf63gzdP19MkurkiVlvq9EmWWEJCmLnHFHuGc87BhzQpKUmWQsCEk0XDAeCEmEJ8YjIYlQMZ4ISeTFGOw3AmfkWz52V4v3tHY8eLwIb2osPv7ETY43i/oBZPiF9wplbmRzdHJlYW0KZW5kb2JqCjEwIDAgb2JqCjw8IC9UeXBlIC9Gb250RGVzY3JpcHRvciAvRm9udE5hbWUgL0FBQUFBQytZdU1pbmNoby1SZWd1bGFyIC9GbGFncyA0IC9Gb250QkJveApbLTQxMiAtMzYzIDEyNDMgMTI5Ml0gL0l0YWxpY0FuZ2xlIDAgL0FzY2VudCA4ODAgL0Rlc2NlbnQgLTIyMiAvQ2FwSGVpZ2h0Cjc0MiAvU3RlbVYgMCAvTGVhZGluZyA1MDAgL1hIZWlnaHQgNDcwIC9BdmdXaWR0aCA5NjkgL01heFdpZHRoIDEzMDIgL0ZvbnRGaWxlMgoxMiAwIFIgPj4KZW5kb2JqCjEyIDAgb2JqCjw8IC9MZW5ndGgxIDcxNzYgL0xlbmd0aCA0NzY5IC9GaWx0ZXIgL0ZsYXRlRGVjb2RlID4+CnN0cmVhbQp4AY04aXAbVZrfa0mt07Es2/KhOK+ltuRDvmLF96X4kK8Q30HtHEiWjyTYuU+SEBHIgQIhBAJsYLgGhoGwTCthpxxqKIaaZZcBNDA7s7sztTs7x5+trZrapaZ2t1gqSe/3umXnGNjaVqv7fcf73ne/J+3bs38GrBAHHdTEFqK7QL1yX8BXeezAPkGDDb8CIC/O7ppb0GDjIYCMr+fmD89qcO7vAAzXt85EpzUYruO7fisiNJiswXfx1oV9OI9dufvwUTq/M5am536MsHMheii9PvwzwsKO6MIMvvFyMbhi156ZNJ2EAfRxxP3fF0GyBYJgUNk4sEM1nAHQ/V3G1yqG0cmvL/3VC789d19m639BpklFp65lHGSDX30WyL/+7o0C858sGxA0A6eScQ6A0XKzSQ+W7UgfNf+JYe64LEElOfgUlQcV+oPBDfSdgT30LwcU+vZAnF7uV+hbfQp9sy9Ov9/XRN/oVej3eufo66Gn6GshhX43FKev9ij0le42+nK3Ql/qUuiLnQr9TucQfWGtQp9fu4deWltK/6JDoc+1X6bPtiv0mbY5erFNoU+3KvSp1hS90KLQJxtX0fONCn2iMUXPNSv08aY5+liTQs82KDRRr9BH67bQM3UperpOoafq4vRkIEUfCSj04TUKPbEmRR9ardB4rUKPV7fRB2su02M1Cj1ardAjVW30gao5erhKoYeqLtODVSI9UJVN91cqdF/lBrq3sozuqXAEFbrbr9Bd/hTd6Z+jO8pTdKFcofPlW+j95fV0e6mPbisN0q1ll+lcmUJnSxU6U7qFTpcoNFYSp1M+hUZ9cRrxrab3eRW6xRukm0UL3VTspRuLFTopDlFJVGjYE6T3elJ0g0ehE545Ou6ppGOCQkfdKTriVujwKoUOUYWuL3qK3lOk0HVFKTo4kEV7Q37a3TlAuzpTtDOYomuDubS1ZYA2N87RpkYfra9ro3UdroJNawJxGiA4qF19ma4uzKA11ZdpdZWHVhUqtLIiTisKjNRfXT5RXqDQstIULXUqtMTXRoObfdZ85yZvnp4Wi7lU1OU5N3mcBuoWhqggrJqgq4boqvjKiaKVVrrSuYe6igpgY36eEzYWslEeG+UWNDj9k9ktjomsFvuEQ7JLQoZndSjDY5tYVH4X3GFAwODRT7wbujpBr5IvrpLjV8nwVXKVXJmAKyRyhVwh+gnbz5RTNulnkj6lnNJLKSkzVZ0aSu1MHU89kfpBypiZ6kCQAR+kPk/9R0pJmYcQeAkBfWbtignBiqtYPRZ1yVojAkYPP0EUmFjxmXJqhfSZZPlUOWWRPpV46SWewCfKKZA+kQQzcpo9JnWaoENA5+EmTD9VTpmkn0rcx8opTvpYeuljoovHDeQaOQ/j/sFFozI6KJuGN8rkjOwdY8/gyKTMn5FhYnJjOEnIOenk449DUeegXDsWvqKLRIo6pUF5mo2DQXUcZ2O7HcdJjusaCSf1unPSXvCDH298sEsdqLjbsX7/3n37b2Pcu8+/dy8+lmf4yW0fFa1JImysPjT5ONRuWMaqVG0KPtNqLMNL09OIbyDfxrFMvSVzGZUWkM+3Y0f+ElYZfg6rdB/AKuhU/rD0vblFuW74e3Bhx6L4fQHegDb8XIBu/MgwpHwFz4MbfkQehU7YSzbBMETh+3AZLoKTtJAuWAsRmIZn4DyEoB2ehFdhDOc9A29hr12PcgbgLGIj8Bg8DpfgN9AK44jRwSg8gbRh2IDy34AfwYfwCdlDHiJnyXnyPEodhj3IcYWswzWboBdhCVe+BK/B25CERfgJPASnUf5z8Ap8D94iQMpJI+kGEwjQCB3Qh6tvRs22w27U7VmU0Y4aDmLPP4+6P6/q+Sy8DG/CO/Au/AK17Vf1fQHfw6hhCNeLwTFcg+l9GTUPQDP6YAD1PwyPIFaHnKMwCQ/j+CKkrv+T8p939P1vBQz/DS18H7TcnDT8I7QABHtPnT518pGHTzwUP/7gsaNHHjh86OCB/fv27tm9a+eOhfn7t2/bOjc7Mx2bikbu27J508ZJKXzvhonxsZHhofX3rBsc6O/rDZVSu8VcQZJWS5fYNWOprICkxYpDa2UFkfku2agi5SG/gEUUdg+Ohnu6XW635BLdclDWe3vYNzqdiC0RJBSBs3AuihgcEwdHJsNCTyKizkLM+B2QRm9kElVaeiRzXeNhOeRHvErR4F4VRkYN7LuL3L9EFgUZhhOJ6STovCgm6EoSdWDoOiuhJZIoT/lFtxieQVFJE9jc45EuHNmWRkToxRWERTtM4Td2r7hI0qPJsCxEZqU+5AbOK6v32CLUiYe0cUQWYoIg815xajiccMskIrrS8GgYPUairoRbdAuStKh8uJJxi26UxUFnUiRnRpJBcmZsMnzNjgeXM+PhKxzhuiLYgoqRFr4mYMRVLHakSCdDMhaBATBIMDJXOJPK77oWBIirVL2KUOEYWqHiNCbEEYgtchrOrvIlfepCQeCQotcowSUJesSZNFxcxUnsQj9gqIIWQ9AUNAdtXAaH3maoK4h5D480ZgJXbSSDuJI4C3VE9CKJJ81Bl8YRR46gpGk8ga/00hOT4as2wGnqExfqZFdlRU+SW+8Xb+XjSBg935Mk6/0RNSd13h4Bs1EOjoUZZ8TllqTuygqWEUJYnHGJUjInJ7GrByWIySjvi/gTYZklBksH0d6MyaXz9sfEUIRxYHLj3Y+o2AYhIk9F/DgU7KEEkoVYlHGDM8npvEmi95J2aEfbeZtsEWc6ZavYuUzpgA6NwjOKUeyUiVPzXI/YI+RvS8TEKcyT4HB4zjUrRVG2HBSjsl7sdCX10Ik1kE/QiJ4krPejNYOYKUP+4Y1YSsxyIZHoFpJBvS8aizK4243VmUiTxO5uVlpLM3qEhByMxiLI0SOpzJUVqEKiR4wK01jiaC76akxkW+YkW2V8MpywTYvTIjo0GExE0WyXEJNcCSmmOhjVQdWgssJwq4ekWwjHKtUbm8XHogBTEXFKQ7Aauhs3dzdiFrlux4kDbDlUdoBpje/EgNgzjRzsG52WdZhcbmEa92uWHzCsVve3MqGIZSYBY6oKT9hbWKYwCOkqhADeCXnuTnDrMhhCMiaD3lul5Yqs97FcC7vl7S55XmL5orFE5fiUkBDsYrPIHmqe9SK1NyIbvL1yPBZFO7BzYO4hYgARQngKsxcFhiKJpYzDaXrf8kryDoz9bSKx8ZFxXJrzMi/I8WEhIgmRCGKxdbtdgmzAtzAbZcnFmuMwro/3MHZofEUTYzgXJFzUJRuxT89GZ0Q3tmDEYaGr3mc66lE7GAvL4EokxIRMUEVvCJlRvE/mff3shfcuvxidwSCy9YTojBr6EKqreodJc/WIbglZOC/zO3McdqMp9oglMBvlzVhtBm9WwpEQmhLh92AztkW9L7Yhgs1bsAshQQ11FDOZOaGfQRIK0hjNWLHqfBTBtFnwJzcbvbcwiPTKO/0as0mVipqNhuVhtii7jeqNg91+mctrRCIznoziHoBdAQPFnGfw9qN7g5h6LjZbkDnccNSmoc3vZ1NdaUx6GmJY55TY3obdX1PBqumrLcoz+bJNvc1e2eTFQMt61EEjG5k5SzLVMSqtzUGVUF3NABzjUmw3YrdqSCQN6L3odpSnbmDMABb5YBSrPiq6FpUfD+OuGUEoIkoSWx5vTDs2QxWd0AQzd6Fk0ze6Ir2StrjVK1uRi5mgIbSnxSvjjUahzoymOQ6XQJSqL1sn7b1ryo8BoSW/qa5jE3Te0ywQWKTpuptxyVsl/7QmjNde/QJ2VOzcsRH1TLARq0F0G7GPoQewoQnymB/3DNW20+oMH6Y4ayEsK0lIhBDmUHoATpBB7CPsAVhaYp/MIbg8Eq9wQExiI3uZxcYkR4zY7bEHi/YMGzb6RCwyjfscbqfoZWh0tbIDDfoDA403xvYAa03jYYNLzyoLK0o+qIUUywr9whiW6QexEzJvaZ40MVpimcgcqU5m3sbSVJ8H/CaU9+ezEqb/32KYCmo0ZbOqCOtGPhNT7NuX0mkBGmALYHPlNDMGmCkGdDTWdCIRi+I5aPMKVqE2XxbiHWhTE5rWlLYNfXMU7R5mq6MOaLAKjoeZ+VZMAjV5rEiwox8/1FLbikQ7Gv4hNrS03tcUBdCBaW7NdehflKfmeZqsFgYTxuYd9Es4CrFvBLlC7JuuJGu6Sm13df20eC2m5juJ4rIwttGLyxIZlCQ2PLLqXQZc0SfY0V3Nauh8aAnCieYkMfrSDJiRdjSzOZGwitqWIrL2fw2PiaAeAUHC7nwnQj6GoU8kTBnfTDHdzZ+hTmDFj1HOWH4zKelysHTJ1i52fsEAymbWh6owvsc+YumGWaEeJ27bDlUUK8XbN8l85nt0NE5Bj+9UmxljXPLbrFrSmri7sePhY7gI89RH6Ay7TPBt8LnZ18Vcp+acjJWx058+rB5juXFCLakTfkHYhuesLoKnLdwocSxgN0Buk09tcgk88GyL4taMfYi1aykfz1Kj7ISL53TRLpBWaNV+vIhaxo3hHqD3hltdTRKe/heVf1vJ+jy6hsNNHr/jCUGwZyEpITjw54B8kpWiPk0TVRzu4rwvzcUsOOlPJFS+5R9flYA/VyHF5QBw1fCi4XPo0/0RFri3oIxbB92cF8p0IdjCfYS/yAGP+do/izbgYSXCbvwXUof9x4Rvgv9r8mDEU7d2teDv1zn4JVlDXiZfc+3cm9xNJBggpQSMAf73OI/HWTbIBBrMyOAyDUbOZLDowKCD6upA1vKnZnW2O8tN3IRkEeI261L87P9cMix+/RV3kvvbG8duNBn0N47fqONO3jjC//6rUWP39X+9/iRfi8+LurwbTTqXriittaYXn+RG/DWra5g1L4KbXCC5qIP5CpjM0JGqWU1q85y5OUZe9JT46tY01JOCelEoFasai2mph+SWiIE6kZZ4qlpEihL6oNNwxvAl/toXgnZDQ0Or4Ck2WVtXT863t3LQEfDXdgQCjqbqAAp25uXmMKklPtFj5NknNysnz6l9kMTQHrZkQ31d1polLtFTV1ffoHcLhhlLgWgz1I70vj2wMi/TYcDLnlXsGcvOfn33vosOx4l8V0H+3lcHRuoNZrHQShxkjiwYvjz2qclhu7C2yWLKK6qqOX4j+2ypJy/PYjZy+UUmU1E+Z/WVf6HLfNPrbVp7webgPzppwWSABXDrntb9Abz4r0N5MN9icRQW++CBeZ/PUVhzaL7Q6OCPzjus0OHv8KOB/tosfKKVfC7aFKhtqA/c5UgG5zHHop0ljKO2Lu0KUsDfH96RnDWYbTar7fivPz+289wD73x3+6TFYrF+/tePrmmdP/tgjqW1fnBTKMiFa8Qf7tty3mQ1mayZHzx54m9+e+7I/ukLmRYLH+JMrh3Bxnt6MDJl0Kkz4f9OHeAL5gYC7qpSAu7CI/MN7pVH5t18RqYFw9PhZxFKLWmuhePPQ8HiggY5AyUYnUBtOj20COWiIbnMKnKYs2fudzgeCU8+4nAsrMjVr52zreAz6zZxFw6+t93ldBVWWioapopWHD+ywslb+3V7+966+YuXV7osunynXu/M15mKfJdJ4dxp3tRu4+b//dXTTU2lbq9Z/Hz7v/CWdpuexaUbOrlsfgUUwxpwBW0GvUFvLVxpqfRajdDxy1QHZltTNQYim2VQOp9qc5dS7S7bsu+K0R/tjvJ19oJLB/JNtsz77fYj6yYP2u2bc+xtVcWY9KU1xUKZhzsVLynTu7JN3ursn3xHgfedLpMu26VHlM5S4Hxf/3qpWBIQi3zuiqpigdUZxoJ8jbEohoKg1WCw8XyRjbiK1Aig77NY4mSrKX9L4axvCsbP7faaPnve/ldyTPas6WzHkb7Rh+32LZk5ZJvuoXhpmT4/35DfnPX+zX+4IS660LFO5linzrjKy/TYAm7uN5jTbeAN5hpa+Qx/YaP50HyjsbDk8Ly70IotqQNrliV0k9oLlkqWFSzLa/wb/FZ3KClZo+a0mu116O4SVtusgBk6UMs9z2W6Mg09r95T7i32zQ48Fiifmnz6+MM5eVyBna8Y6RkM11aUlUp956pLpfW7G0aK/Xm6T9de4m0r3q8saQ+4i8tpc1/z5JmhvkD/UXuWbbaptrGmL+gv89G2YMOm+e7WbB6takOrXtE/B1XgDjrc7syyoqPzZVYCmfkPzFsz0aSUv7ZWLVL08lKHUfOYX05eUfdNVZv9xfGcnE31a1bx+S5+72u2bEPmwA/1ZaXdAw08b7Wa+di+48E2vW435yrU57a1jDhyn6nS25pX8DctFxvrGvkM5OEXQqGLxMm6LwEHftnFQx7AWnZ1+aX992zbEdu6s3J0Zm7/fHQPkv8X9p4foAplbmRzdHJlYW0KZW5kb2JqCjEzIDAgb2JqCjw8IC9UaXRsZSAo/v9lh2b4XDAwMCBcMDAwMSkgL1Byb2R1Y2VyICj+/1wwMDBtXDAwMGFcMDAwY1wwMDBPXDAwMFNcMDAwIDDQMPwwuDDnMPNcMDAwMVwwMDA0XDAwMC5cMDAwM1wwMDAuXDAwMDH/XDAxMDDTMOswyVwwMDAyXDAwMDNcMDAwRFwwMDA2XDAwMDD/XDAxMVwwMDAgXDAwMFFcMDAwdVwwMDBhXDAwMHJcMDAwdFwwMDB6XDAwMCBcMDAwUFwwMDBEXDAwMEZcMDAwQ1wwMDBvXDAwMG5cMDAwdFwwMDBlXDAwMHhcMDAwdCkKL0F1dGhvciAo/v9biVBcMDE1KSAvQ3JlYXRvciAoV29yZCkgL0NyZWF0aW9uRGF0ZSAoRDoyMDI0MDMyNzA4NTU0MFowMCcwMCcpCi9Nb2REYXRlIChEOjIwMjQwMzI3MDg1NTQwWjAwJzAwJykgPj4KZW5kb2JqCnhyZWYKMCAxNAowMDAwMDAwMDAwIDY1NTM1IGYgCjAwMDAwMDAyNTkgMDAwMDAgbiAKMDAwMDAwMzIxMyAwMDAwMCBuIAowMDAwMDAwMDIyIDAwMDAwIG4gCjAwMDAwMDAzNjkgMDAwMDAgbiAKMDAwMDAwMzE3OCAwMDAwMCBuIAowMDAwMDAwMDAwIDAwMDAwIG4gCjAwMDAwMDMzNTEgMDAwMDAgbiAKMDAwMDAwMDQ2NiAwMDAwMCBuIAowMDAwMDAzMzAyIDAwMDAwIG4gCjAwMDAwMDM5MDQgMDAwMDAgbiAKMDAwMDAwMzU1NCAwMDAwMCBuIAowMDAwMDA0MTYyIDAwMDAwIG4gCjAwMDAwMDkwMTkgMDAwMDAgbiAKdHJhaWxlcgo8PCAvU2l6ZSAxNCAvUm9vdCA5IDAgUiAvSW5mbyAxMyAwIFIgL0lEIFsgPDk5NzQ1NGRmZTlmNTM3Y2QwOTYzYTgzY2I4ZTliMjM3Pgo8OTk3NDU0ZGZlOWY1MzdjZDA5NjNhODNjYjhlOWIyMzc+IF0gPj4Kc3RhcnR4cmVmCjkzOTQKJSVFT0YK';
        $this->assertSame($expect, $result);
    }
}
