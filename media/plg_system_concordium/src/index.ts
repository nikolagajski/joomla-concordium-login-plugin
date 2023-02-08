import {detectConcordiumProvider} from '@concordium/browser-wallet-api-helpers';
import {AccountTransactionSignature} from "@concordium/web-sdk";
import axios, {AxiosError, AxiosResponse} from "axios";

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

interface JoomlaText {
    _(key: string, def?: string | undefined): string,
}

interface JoomlaInterface {
    Text: JoomlaText,
    getOptions(key: string, def?: any): any,
}

// @ts-ignore
const Joomla: JoomlaInterface = window.Joomla

interface ConcordiumButton extends HTMLButtonElement{
    changeContent(value: string, divSelector?: string): void
}
class LoginButtons {
    private buttons: HTMLCollectionOf<ConcordiumButton>;

    constructor(className: string) {
        // @ts-ignore
        this.buttons = document.getElementsByClassName(className)
        this.apply(function (n: ConcordiumButton) {
            n.innerHTML = n.innerHTML.replace(
                Joomla.Text._('PLG_SYSTEM_CONCORDIUM_LOGIN_LABEL'),
                '<span class="concordiumButtonMessage">' + Joomla.Text._('PLG_SYSTEM_CONCORDIUM_LOGIN_LABEL') + '</span>');

            // @ts-ignore
            n.changeContent = function (value: string, selector: string = '.concordiumButtonMessage'): void {
                const selected = this.querySelector(selector)
                if (selected) {
                    selected.innerHTML = value
                }
            }
        })
    }

    public apply(callback: (n: ConcordiumButton) => void) {
        for (var i = 0; i < this.buttons.length; i++) {
            callback(this.buttons[i])
        }
    }
}

export async function run() {
    const buttons = new LoginButtons('plg_system_concordium_login_button')
    buttons.apply(btn => btn.disabled = true)

    // @ts-ignore
    const rootUri: string = Joomla.getOptions('system.paths').rootFull;

    buttons.apply(btn => {
        btn.disabled = true
        btn.changeContent(Joomla.Text._('PLG_SYSTEM_CONCORDIUM_APP_IS_NOT_INSTALLED'))
    })

    detectConcordiumProvider()
        .then((provider) => {
            // The API is ready for use.
            async function connect(btn: ConcordiumButton): Promise<any> {
                provider
                    .connect()
                    .then(async (accountAddress): Promise<void> => {
                        btn.changeContent(Joomla.Text._('PLG_SYSTEM_CONCORDIUM_SIGNING_NONCE'))
                        // The wallet is connected to the dApp.
                        if (accountAddress === undefined) {
                            throw new Error
                        }

                        const returnInput = btn.form?.querySelector('input[name="return"]')
                        const rememberInput = btn.form?.querySelector('input[name="remember"]')
                        let returnVal = ''
                        let remember  = false

                        if (returnInput instanceof HTMLInputElement)
                        {
                            returnVal = returnInput.value
                        }

                        if (rememberInput instanceof HTMLInputElement)
                        {
                            remember = rememberInput.checked
                        }

                        const res:AxiosResponse<JoomlaJson<NonceJson>> = await axios<JoomlaJson<NonceJson>>({
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
                            throw new Error
                        }

                        const text = res.data.data.nonce
                        const signed: AccountTransactionSignature = await provider.signMessage(accountAddress, text)

                        const res2:AxiosResponse<JoomlaJson<AuthJson>> = await axios<JoomlaJson<AuthJson>>({
                            method: 'post',
                            url: rootUri + 'index.php?option=concordium&task=auth',
                            data: {
                                accountAddress: accountAddress,
                                signed: signed,
                                text: text,
                                return: returnVal,
                                remember: remember,
                            },
                            headers: {
                                'Content-Type': 'multipart/form-data',
                            }
                        });

                        if (res2.status != 200) {
                            throw new Error
                        }

                        btn.disabled = false
                        btn.changeContent(Joomla.Text._('PLG_SYSTEM_CONCORDIUM_SIGNING_NONCE_SIGNED'))

                       window.location.href = res2.data.data.redirect
                    })
                    .catch((e: AxiosError | Error) => {
                        if (e instanceof AxiosError
                            && e.response && e.response.data && e.response.data.message) {
                            btn.changeContent(e.response.data.message)
                        } else {
                            btn.changeContent(Joomla.Text._('PLG_SYSTEM_CONCORDIUM_WALLET_REJECT'))
                        }

                        btn.disabled = false
                    });
            }

            buttons.apply(btn => {
                btn.disabled = false
                btn.changeContent(Joomla.Text._('PLG_SYSTEM_CONCORDIUM_LOGIN_LABEL'))
                btn.addEventListener(
                    'click',
                    (event) => {
                        event.preventDefault()
                        btn.disabled = true
                        btn.changeContent(Joomla.Text._('PLG_SYSTEM_CONCORDIUM_CONNECTING'))
                        connect(btn)
                    },
                    false
                )
            })
        })
        .catch((e) => {
            console.log('Connection to the Concordium browser wallet timed out.')
        });
}

run()
