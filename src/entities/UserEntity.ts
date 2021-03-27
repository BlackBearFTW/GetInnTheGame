class UserEntity {
    public ID: number = 0;
    public EMAIL: string = "";
    public FIRSTNAME: string = "";
    public LASTNAME: string = "";
    public ADMIN: boolean = false;
    public POINTS: number = 0;
    public COMPANY_ID: number = 0;
    public QR: string = "";
    public PASSWORD: string = "";
    public VERIFY_TOKEN: string = "";
    public PASSWORD_RESET_TOKEN = "";
}

export default UserEntity;