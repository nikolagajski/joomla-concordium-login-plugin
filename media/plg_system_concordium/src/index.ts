// https://github.com/Concordium/concordium-browser-wallet/tree/main/packages/browser-wallet-api-helpers
import {detectConcordiumProvider} from '@concordium/browser-wallet-api-helpers';
import {AccountTransactionSignature, IdStatement, IdStatementBuilder} from "@concordium/web-sdk";
import {AccountInfoSimple, AttributesKeys} from "@concordium/common-sdk/lib/types";
import {AccountInfo, verifyMessageSignature} from "@concordium/common-sdk";
import axios from "axios";
import { credentials, Metadata } from "@grpc/grpc-js";
import { ConcordiumNodeClient } from "@concordium/node-sdk";
import {AccountAddress} from "@concordium/common-sdk/lib/types/accountAddress";

class LoginButtons {
    private buttons: HTMLCollectionOf<HTMLButtonElement>;

    constructor(className: string) {
        // @ts-ignore
        this.buttons = document.getElementsByClassName(className)
    }

    public apply(callback: (n: HTMLButtonElement) => void) {
        for (var i = 0; i < this.buttons.length; i++) {
            //this.buttons[i].style.visibility = 'hidden';
            callback(this.buttons[i])
        }
    }
}

// class Concordium {
//
// }

//{"success":true,"message":null,"messages":null,"data":{"nonce":"390693"}}
interface JoomlaJson<T> {
    success: boolean
    message: string | null
    messages: Array<any> | null
    data: T
}

interface AuthJson {
    redirect: string
}
interface NonceJson {
    nonce: string
}
export async function run() {
    console.log('ok fine')

    const buttons = new LoginButtons('plg_system_concordium_login_button')
    buttons.apply(btn => btn.disabled = true)

    // @ts-ignore
    const rootUri: string = Joomla.getOptions('system.paths').rootFull;

    detectConcordiumProvider()
        .then((provider) => {
            // The API is ready for use.
            async function connect(btn: HTMLButtonElement): Promise<any> {
                provider
                    .connect()
                    .then(async (accountAddress): Promise<void> => {
                        // The wallet is connected to the dApp.

                        if (accountAddress === undefined) {
                            return
                        }

                       // btn.disabled = true

                        const res = await axios<JoomlaJson<NonceJson>>({
                            method: 'post',
                            url: rootUri + 'index.php?option=concordium&task=nonce',
                            data: {
                                accountAddress: accountAddress,
                            },
                            headers: {
                                'Content-Type': 'multipart/form-data',
                            }
                        });

                        if (res.status != 200) {
                            return
                        }

                        console.log(res)

                        const text = res.data.data.nonce
                        const signed: AccountTransactionSignature = await provider.signMessage(accountAddress, text)

                        const res2 = await axios<JoomlaJson<AuthJson>>({
                            method: 'post',
                            url: rootUri + 'index.php?option=concordium&task=auth',
                            data: {
                                accountAddress: accountAddress,
                                signed: signed,
                                text: text,
                            },
                            headers: {
                                'Content-Type': 'multipart/form-data',
                            }
                        });

                        if (res2.status != 200) {
                            console.log(res2)
                        }

                        btn.disabled = false

                        window.location.href = res2.data.data.redirect
                    })
                    .catch((e) => {
                        console.log(e)
                        console.log('Connection to the Concordium browser wallet was rejected.')
                    });
            }

            buttons.apply(btn => {
                btn.disabled = false
                btn.addEventListener(
                    'click',
                    (event) => {
                        event.preventDefault()
                        connect(btn)
                    },
                    false
                )
            })
        })
        .catch((e) => {
            console.log(e)
            console.log('Connection to the Concordium browser wallet timed out.')
        });
}

run()
