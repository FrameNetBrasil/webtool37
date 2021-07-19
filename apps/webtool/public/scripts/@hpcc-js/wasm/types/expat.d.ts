export declare type Attributes = {
    [key: string]: string;
};
export interface IParser {
    startElement(tag: string, attrs: Attributes): void;
    endElement(tag: string): void;
    characterData(content: string): void;
}
declare class StackElement {
    readonly tag: string;
    readonly attrs: Attributes;
    private _content;
    get content(): string;
    constructor(tag: string, attrs: Attributes);
    appendContent(content: string): void;
}
export declare class StackParser implements IParser {
    private _stack;
    parse(xml: string): Promise<boolean>;
    top(): StackElement;
    startElement(tag: string, attrs: Attributes): StackElement;
    endElement(tag: string): StackElement;
    characterData(content: string): void;
}
export declare function parse(xml: string, callback: IParser, wasmFolder?: string, wasmBinary?: Uint8Array): Promise<boolean>;
export {};
//# sourceMappingURL=expat.d.ts.map