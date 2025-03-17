
--tạo id_account
DELIMITER $$

CREATE TRIGGER before_insert_accounts
BEFORE INSERT ON Accounts
FOR EACH ROW
BEGIN
    DECLARE next_id INT;

    -- Kiểm tra nếu bảng rỗng thì gán id_account = 1
    IF NOT EXISTS (SELECT 1 FROM Accounts) THEN
        SET NEW.id_account = 1;
    ELSE
        -- Tìm giá trị id_account nhỏ nhất bị khuyết
        SELECT MIN(t1.id_account + 1) INTO next_id
        FROM Accounts t1
        WHERE NOT EXISTS (SELECT 1 FROM Accounts t2 WHERE t2.id_account = t1.id_account + 1);

        -- Nếu không có giá trị khuyết, gán id_account = MAX(id_account) + 1
        IF next_id IS NULL THEN
            SELECT MAX(id_account) + 1 INTO next_id FROM Accounts;
        END IF;

        -- Gán giá trị mới cho id_account
        SET NEW.id_account = next_id;
    END IF;
END $$

DELIMITER ;


--tạo id_delivery tự động
DELIMITER $$

CREATE TRIGGER before_insert_delivery
BEFORE INSERT ON Delivery_Information
FOR EACH ROW
BEGIN
    DECLARE next_id INT;
    DECLARE new_id VARCHAR(20);

    -- Kiểm tra nếu bảng rỗng thì gán id_delivery = 'DEL1'
    IF NOT EXISTS (SELECT 1 FROM Delivery_Information) THEN
        SET new_id = 'DEL1';
    ELSE
        -- Tìm giá trị id_delivery nhỏ nhất bị khuyết (loại bỏ 'DEL' và convert sang INT)
        SELECT MIN(t1.id_num + 1) INTO next_id
        FROM (
            SELECT CAST(SUBSTRING(id_delivery, 4) AS UNSIGNED) AS id_num 
            FROM Delivery_Information
        ) t1
        WHERE NOT EXISTS (
            SELECT 1 FROM (
                SELECT CAST(SUBSTRING(id_delivery, 4) AS UNSIGNED) AS id_num 
                FROM Delivery_Information
            ) t2 
            WHERE t2.id_num = t1.id_num + 1
        );

        -- Nếu không có giá trị khuyết, lấy MAX(id_delivery) + 1
        IF next_id IS NULL THEN
            SELECT MAX(CAST(SUBSTRING(id_delivery, 4) AS UNSIGNED)) + 1 INTO next_id FROM Delivery_Information;
        END IF;

        -- Ghép 'DEL' với số ID mới, đảm bảo không vượt quá 20 ký tự
        SET new_id = CONCAT('DEL', next_id);
    END IF;

    -- Gán giá trị mới cho id_delivery
    SET NEW.id_delivery = new_id;
END $$

DELIMITER ;
